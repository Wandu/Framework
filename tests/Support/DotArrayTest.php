<?php
namespace Wandu\Support;

use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

class DotArrayTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $arr = new DotArray([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        $this->assertSame('foo string!', $arr->get('foo'));
        $this->assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $arr->get('bar'));
        $this->assertSame('bar1 string!', $arr->get('bar.bar1'));

        $this->assertSame(null, $arr->get('null'));
        $this->assertSame(null, $arr->get('null.isnull'));
    }

    public function testGetDefault()
    {
        $arr = new DotArray([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        $this->assertNull($arr->get('bar.bar3'));
        $this->assertNull($arr->get('unknown.something'));

        $this->assertSame('unknown', $arr->get('bar.bar3', 'unknown'));
        $this->assertSame('unknown', $arr->get('unknown.something', 'unknown'));

        $this->assertSame(null, $arr->get('null', 'unknown'));
        $this->assertSame('unknown', $arr->get('null.isnull', 'unknown'));
    }

    public function testHas()
    {
        $arr = new DotArray([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        $this->assertTrue($arr->has('foo'));
        $this->assertTrue($arr->has('bar'));

        $this->assertTrue($arr->has('bar.bar1'));
        $this->assertFalse($arr->has('bar.bar3'));

        $this->assertTrue($arr->has('null'));
        $this->assertFalse($arr->has('null.isnull'));
    }

    public function testSet()
    {
        $arr = new DotArray([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
        ]);

        $arr->set('foo', 'foo string!!');
        $this->assertSame('foo string!!', $arr->get('foo'));

        $arr->set('foo.bar', 'foo.bar string!');
        $this->assertSame([
            'bar' => 'foo.bar string!'
        ], $arr->get('foo'));

        $arr->set('bar.bar2', 'bar2 string!!');
        $arr->set('bar.bar3', 'bar3 string!');
        $this->assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!!',
            'bar3' => 'bar3 string!',
        ], $arr->get('bar'));

        $arr->set('baz', 'baz string!');
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
        ], $arr->getRawData());
    }

    public function testRemove()
    {
        $arr = new DotArray([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        $this->assertTrue($arr->has('foo'));
        $this->assertTrue($arr->remove('foo'));
        $this->assertFalse($arr->has('foo'));

        $this->assertFalse($arr->has('bar.bar3'));
        $this->assertFalse($arr->remove('bar.bar3'));

        $this->assertFalse($arr->has('bar.bar2.unknown'));
        $this->assertFalse($arr->remove('bar.bar2.unknown'));
        $this->assertEquals([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $arr->get('bar'));

        $this->assertTrue($arr->has('bar.bar2'));
        $this->assertTrue($arr->remove('bar.bar2'));
        $this->assertFalse($arr->has('bar.bar2'));
        $this->assertEquals([
            'bar1' => 'bar1 string!',
        ], $arr->get('bar'));

        $this->assertFalse($arr->has('null.isnull'));
        $this->assertFalse($arr->remove('null.isnull'));

        $this->assertTrue($arr->has('null'));
        $this->assertTrue($arr->remove('null'));
        $this->assertFalse($arr->has('null'));

        $this->assertEquals([
            'bar' => [
                'bar1' => 'bar1 string!',
            ]
        ], $arr->getRawData());
    }

    public function testArrayAccess()
    {
        $arr = new DotArray([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        $this->assertSame('foo string!', $arr['foo']);
        $this->assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $arr['bar']);
        $this->assertSame('bar1 string!', $arr['bar.bar1']);

        $this->assertSame(null, $arr['null']);
        $this->assertSame(null, $arr['null.isnull']);

        // with default
        $this->assertSame('unknown', $arr['bar.bar3||unknown']);
        $this->assertSame('unknown', $arr['unknown.something||unknown']);

        $this->assertSame(null, $arr['null||unknown']);
        $this->assertSame('unknown', $arr['null.isnull||unknown']);
    }
    
    public function testSubset()
    {
        $arr = new DotArray([
            'foo' => 'foo string!',
            'bar' => [
                'bar1' => 'bar1 string!',
                'bar2' => 'bar2 string!',
            ],
            'null' => null,
        ]);

        $this->assertInstanceOf(DotArray::class, $arr->subset('bar'));
        $this->assertEquals([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $arr->subset('bar')->get(''));

        try {
            $arr->subset('foo');
            $this->fail();
        } catch (InvalidArgumentException $e) {}
        try {
            $arr->subset('null');
            $this->fail();
        } catch (InvalidArgumentException $e) {}
        try {
            $arr->subset('bar.unknown');
            $this->fail();
        } catch (InvalidArgumentException $e) {}
  
    }
}
