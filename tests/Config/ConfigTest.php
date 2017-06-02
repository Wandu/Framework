<?php
namespace Wandu\Config;

use PHPUnit\Framework\TestCase;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Config\Exception\NotAllowedMethodException;
use InvalidArgumentException;

class ConfigTest extends TestCase
{
    public function testGet()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertSame('foo string!', $config->get('foo'));
        static::assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config->get('bar'));
        static::assertSame('bar1 string!', $config->get('bar.bar1'));

        static::assertSame(null, $config->get('null'));
        static::assertSame(null, $config->get('null.isnull'));
    }

    public function testGetDefault()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertNull($config->get('bar.bar3'));
        static::assertNull($config->get('unknown.something'));

        static::assertSame('unknown', $config->get('bar.bar3', 'unknown'));
        static::assertSame('unknown', $config->get('unknown.something', 'unknown'));

        static::assertSame(null, $config->get('null', 'unknown'));
        static::assertSame('unknown', $config->get('null.isnull', 'unknown'));
    }

    public function testHas()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertTrue($config->has('foo'));
        static::assertTrue($config->has('bar'));

        static::assertTrue($config->has('bar.bar1'));
        static::assertFalse($config->has('bar.bar3'));

        static::assertTrue($config->has('null'));
        static::assertFalse($config->has('null.isnull'));
    }

    public function testSetNowAllowed()
    {
        $config = new Config([]);

        try {
            $config->set('foo', 'foo string!!');
            static::fail();
        } catch (NotAllowedMethodException $exception) {
            static::addToAssertionCount(1);
        }
    }

    public function testSet()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
        ], false);

        $config->set('foo', 'foo string!!');
        static::assertSame('foo string!!', $config->get('foo'));

        $config->set('foo.bar', 'foo.bar string!');
        static::assertSame([
            'bar' => 'foo.bar string!'
        ], $config->get('foo'));

        $config->set('bar.bar2', 'bar2 string!!');
        $config->set('bar.bar3', 'bar3 string!');
        static::assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!!',
            'bar3' => 'bar3 string!',
        ], $config->get('bar'));

        $config->set('baz', 'baz string!');
        static::assertSame([
            'foo' => [
                'bar' => 'foo.bar string!',
            ],
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!!',
                'bar3' => 'bar3 string!',
            ],
            'baz' => 'baz string!'
        ], $config->getRawData());
    }

    public function testRemove()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ], false);

        static::assertTrue($config->has('foo'));
        static::assertTrue($config->remove('foo'));
        static::assertFalse($config->has('foo'));

        static::assertFalse($config->has('bar.bar3'));
        static::assertFalse($config->remove('bar.bar3'));

        static::assertFalse($config->has('bar.bar2.unknown'));
        static::assertFalse($config->remove('bar.bar2.unknown'));
        static::assertEquals([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config->get('bar'));

        static::assertTrue($config->has('bar.bar2'));
        static::assertTrue($config->remove('bar.bar2'));
        static::assertFalse($config->has('bar.bar2'));
        static::assertEquals([
            'bar1' => 'bar1 string!',
        ], $config->get('bar'));

        static::assertFalse($config->has('null.isnull'));
        static::assertFalse($config->remove('null.isnull'));

        static::assertTrue($config->has('null'));
        static::assertTrue($config->remove('null'));
        static::assertFalse($config->has('null'));
        
        static::assertEquals([
            'bar' => [
                'bar1' => 'bar1 string!',
            ]
        ], $config->getRawData());
    }
    
    public function testArrayAccess()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertSame('foo string!', $config['foo']);
        static::assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config['bar']);
        static::assertSame('bar1 string!', $config['bar.bar1']);

        static::assertSame(null, $config['null']);
        static::assertSame(null, $config['null.isnull']);

        // with default
        static::assertSame('unknown', $config['bar.bar3||unknown']);
        static::assertSame('unknown', $config['unknown.something||unknown']);

        static::assertSame(null, $config['null||unknown']);
        static::assertSame('unknown', $config['null.isnull||unknown']);
    }
    
    public function testReadOnly()
    {
        $config = new Config([
            'foo' => 'foo string!',
        ]);
        
        $config->get('foo');
        try {
            $config->set('bar', 'something');
            static::fail();
        } catch (NotAllowedMethodException $e) {
            static::addToAssertionCount(1);
        }
        try {
            $config->remove('foo');
            static::fail();
        } catch (NotAllowedMethodException $e) {
            static::addToAssertionCount(1);
        }
    }
    
    public function testSubset()
    {
        $config = new Config([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        static::assertInstanceOf(ConfigInterface::class, $config->subset('bar'));
        static::assertEquals([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config->subset('bar')->get(''));

        try {
            $config->subset('foo');
            $this->fail();
        } catch (InvalidArgumentException $e) {}
        try {
            $config->subset('null');
            $this->fail();
        } catch (InvalidArgumentException $e) {}
        try {
            $config->subset('bar.unknown');
            $this->fail();
        } catch (InvalidArgumentException $e) {}
    }
}
