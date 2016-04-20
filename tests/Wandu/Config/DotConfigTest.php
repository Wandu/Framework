<?php
namespace Wandu\Config;

use PHPUnit_Framework_TestCase;

class DotConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $config = new DotConfig([
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
        $config = new DotConfig([
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

    public function testSetNowAllowed()
    {
        $config = new DotConfig([]);

        try {
            $config->set('foo', 'foo string!!');
            $this->fail();
        } catch (NotAllowedMethodException $exception) {
        }
    }

    public function testSet()
    {
        $config = new DotConfig([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
        ], false);

        $config->set('foo', 'foo string!!');
        $this->assertSame('foo string!!', $config->get('foo'));

        $config->set('foo.bar', 'foo.bar string!');
        $this->assertSame('foo.bar string!', $config->get('foo.bar'));
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
        ], $config->toArray());
    }
}
