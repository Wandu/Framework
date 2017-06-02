<?php
namespace Wandu\Support;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class DotArrayTest extends TestCase
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

        static::assertSame('foo string!', $arr->get('foo'));
        static::assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $arr->get('bar'));
        static::assertSame('bar1 string!', $arr->get('bar.bar1'));

        static::assertSame(null, $arr->get('null'));
        static::assertSame(null, $arr->get('null.isnull'));
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

        static::assertNull($arr->get('bar.bar3'));
        static::assertNull($arr->get('unknown.something'));

        static::assertSame('unknown', $arr->get('bar.bar3', 'unknown'));
        static::assertSame('unknown', $arr->get('unknown.something', 'unknown'));

        static::assertSame(null, $arr->get('null', 'unknown'));
        static::assertSame('unknown', $arr->get('null.isnull', 'unknown'));
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

        static::assertTrue($arr->has('foo'));
        static::assertTrue($arr->has('bar'));

        static::assertTrue($arr->has('bar.bar1'));
        static::assertFalse($arr->has('bar.bar3'));

        static::assertTrue($arr->has('null'));
        static::assertFalse($arr->has('null.isnull'));
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
        static::assertSame('foo string!!', $arr->get('foo'));

        $arr->set('foo.bar', 'foo.bar string!');
        static::assertSame([
            'bar' => 'foo.bar string!'
        ], $arr->get('foo'));

        $arr->set('bar.bar2', 'bar2 string!!');
        $arr->set('bar.bar3', 'bar3 string!');
        static::assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!!',
            'bar3' => 'bar3 string!',
        ], $arr->get('bar'));

        $arr->set('baz', 'baz string!');
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

        static::assertTrue($arr->has('foo'));
        static::assertTrue($arr->remove('foo'));
        static::assertFalse($arr->has('foo'));

        static::assertFalse($arr->has('bar.bar3'));
        static::assertFalse($arr->remove('bar.bar3'));

        static::assertFalse($arr->has('bar.bar2.unknown'));
        static::assertFalse($arr->remove('bar.bar2.unknown'));
        static::assertEquals([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $arr->get('bar'));

        static::assertTrue($arr->has('bar.bar2'));
        static::assertTrue($arr->remove('bar.bar2'));
        static::assertFalse($arr->has('bar.bar2'));
        static::assertEquals([
            'bar1' => 'bar1 string!',
        ], $arr->get('bar'));

        static::assertFalse($arr->has('null.isnull'));
        static::assertFalse($arr->remove('null.isnull'));

        static::assertTrue($arr->has('null'));
        static::assertTrue($arr->remove('null'));
        static::assertFalse($arr->has('null'));

        static::assertEquals([
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

        static::assertSame('foo string!', $arr['foo']);
        static::assertSame([
            'bar1' => 'bar1 string!',
            'bar2' => 'bar2 string!',
        ], $arr['bar']);
        static::assertSame('bar1 string!', $arr['bar.bar1']);

        static::assertSame(null, $arr['null']);
        static::assertSame(null, $arr['null.isnull']);

        // with default
        static::assertSame('unknown', $arr['bar.bar3||unknown']);
        static::assertSame('unknown', $arr['unknown.something||unknown']);

        static::assertSame(null, $arr['null||unknown']);
        static::assertSame('unknown', $arr['null.isnull||unknown']);
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

        static::assertInstanceOf(DotArray::class, $arr->subset('bar'));
        static::assertEquals([
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
