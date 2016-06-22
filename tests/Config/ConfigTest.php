<?php
namespace Wandu\Config;

use PHPUnit_Framework_TestCase;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Config\Exception\NotAllowedMethodException;
use InvalidArgumentException;

class ConfigTest extends PHPUnit_Framework_TestCase
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

        $this->assertSame('foo string!', $config->get('foo'));
        $this->assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config->get('bar'));
        $this->assertSame('bar1 string!', $config->get('bar.bar1'));

        $this->assertSame(null, $config->get('null'));
        $this->assertSame(null, $config->get('null.isnull'));
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

        $this->assertNull($config->get('bar.bar3'));
        $this->assertNull($config->get('unknown.something'));

        $this->assertSame('unknown', $config->get('bar.bar3', 'unknown'));
        $this->assertSame('unknown', $config->get('unknown.something', 'unknown'));

        $this->assertSame(null, $config->get('null', 'unknown'));
        $this->assertSame('unknown', $config->get('null.isnull', 'unknown'));
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

        $this->assertTrue($config->has('foo'));
        $this->assertTrue($config->has('bar'));

        $this->assertTrue($config->has('bar.bar1'));
        $this->assertFalse($config->has('bar.bar3'));

        $this->assertTrue($config->has('null'));
        $this->assertFalse($config->has('null.isnull'));
    }

    public function testSetNowAllowed()
    {
        $config = new Config([]);

        try {
            $config->set('foo', 'foo string!!');
            $this->fail();
        } catch (NotAllowedMethodException $exception) {
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
        $this->assertSame('foo string!!', $config->get('foo'));

        $config->set('foo.bar', 'foo.bar string!');
        $this->assertSame([
            'bar' => 'foo.bar string!'
        ], $config->get('foo'));

        $config->set('bar.bar2', 'bar2 string!!');
        $config->set('bar.bar3', 'bar3 string!');
        $this->assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!!',
            'bar3' => 'bar3 string!',
        ], $config->get('bar'));

        $config->set('baz', 'baz string!');
        $this->assertSame([
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

        $this->assertTrue($config->has('foo'));
        $this->assertTrue($config->remove('foo'));
        $this->assertFalse($config->has('foo'));

        $this->assertFalse($config->has('bar.bar3'));
        $this->assertFalse($config->remove('bar.bar3'));

        $this->assertFalse($config->has('bar.bar2.unknown'));
        $this->assertFalse($config->remove('bar.bar2.unknown'));
        $this->assertEquals([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config->get('bar'));

        $this->assertTrue($config->has('bar.bar2'));
        $this->assertTrue($config->remove('bar.bar2'));
        $this->assertFalse($config->has('bar.bar2'));
        $this->assertEquals([
            'bar1' => 'bar1 string!',
        ], $config->get('bar'));

        $this->assertFalse($config->has('null.isnull'));
        $this->assertFalse($config->remove('null.isnull'));

        $this->assertTrue($config->has('null'));
        $this->assertTrue($config->remove('null'));
        $this->assertFalse($config->has('null'));
        
        $this->assertEquals([
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

        $this->assertSame('foo string!', $config['foo']);
        $this->assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $config['bar']);
        $this->assertSame('bar1 string!', $config['bar.bar1']);

        $this->assertSame(null, $config['null']);
        $this->assertSame(null, $config['null.isnull']);

        // with default
        $this->assertSame('unknown', $config['bar.bar3||unknown']);
        $this->assertSame('unknown', $config['unknown.something||unknown']);

        $this->assertSame(null, $config['null||unknown']);
        $this->assertSame('unknown', $config['null.isnull||unknown']);
    }
    
    public function testReadOnly()
    {
        $config = new Config([
            'foo' => 'foo string!',
        ]);
        
        $config->get('foo');
        try {
            $config->set('bar', 'something');
            $this->fail();
        } catch (NotAllowedMethodException $e) {
        }
        try {
            $config->remove('foo');
            $this->fail();
        } catch (NotAllowedMethodException $e) {
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

        $this->assertInstanceOf(ConfigInterface::class, $config->subset('bar'));
        $this->assertEquals([
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
