<?php
namespace Wandu\Collection;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;
use Wandu\Assertions;
use Wandu\Collection\Contracts\ListInterface;

class ArrayMapTest extends PHPUnit_Framework_TestCase
{
    use Assertions;

    public function testToString()
    {
        $map = new ArrayMap([
            'a' => 'a string',
            'b' => 30,
            'c' => 30.33,
            'd' => null,
            'e' => new stdClass,
            'f' => [1, 2, 3, 4],
        ]);
        $expected = <<<TEXT
Wandu\Collection\HashMap [
    "a" => "a string",
    "b" => 30,
    "c" => 30.33,
    "d" => null,
    "e" => [stdClass],
    "f" => [array],
]
TEXT;

        static::assertEquals($expected, $map->__toString());
    }

    public function testToArrayAndAll()
    {
        $map = new ArrayMap([
            'name' => 'olympic',
            'ages' => new ArrayList(['2004', '2010', '2014', '2018']),
        ]);

        static::assertEquals([
            'name' => 'olympic',
            'ages' => new ArrayList(['2004', '2010', '2014', '2018']),
        ], $map->all());
        static::assertEquals([
            'name' => 'olympic',
            'ages' => ['2004', '2010', '2014', '2018'],
        ], $map->toArray());
    }
    
    public function testCount()
    {
        $map = new ArrayMap([1, 2, 3, 4, 5]);
        static::assertEquals(5, count($map));
    }
    
    public function testOffsetGetAndSet()
    {
        $map = new ArrayMap();
        try {
            $map[] = 30;
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                'Argument 1 passed to Wandu\Collection\HashMap::offsetSet must be not null',
                $e->getMessage()
            );
        }

        static::assertEquals(null, $map['unknown']);

        $map['foo'] = 'foo string';
        static::assertEquals(1, count($map));
        static::assertEquals('foo string', $map['foo']);
        
        $map['foo'] = 'overwrite foo string';
        static::assertEquals(1, count($map));
        static::assertEquals('overwrite foo string', $map['foo']);

        $map['bar'] = 'bar string';
        static::assertEquals(2, count($map));
        static::assertEquals('bar string', $map['bar']);
    }

    public function testOffsetExistsAndUnset()
    {
        $map = new ArrayMap();

        static::assertFalse(isset($map['foo']));

        $map['foo'] = 'foo string';
        static::assertEquals(1, count($map));
        static::assertTrue(isset($map['foo']));

        unset($map['foo']);
        static::assertEquals(0, count($map));
        static::assertFalse(isset($map['foo']));
    }
    
    public function testIterator()
    {
        $expected = [
            'a' => 'a string',
            'b' => 30,
            'c' => 30.33,
            'd' => null,
            'e' => new stdClass,
            'f' => [1, 2, 3, 4],
        ];
        $map = new ArrayMap($expected);
        
        $actual = [];
        foreach ($map as $key => $value) {
            $actual[$key] = $value;
        }
        
        static::assertEquals($expected, $actual);
    }
    
    public function testSerialize()
    {
        $map = new ArrayMap([
            'a' => 'a string',
            'b' => 30,
            'c' => 30.33,
            'd' => null,
            'e' => new stdClass,
            'f' => [1, 2, 3, 4],
        ]);
        
        $serializedMap = unserialize(serialize($map));
        static::assertEquals($serializedMap, $map);
    }
    
    public function testContains()
    {
        $map = new ArrayMap([
            'a' => 'a string',
            'b' => 30,
            'c' => 30.33,
            'd' => null,
            'e' => new stdClass,
            'f' => [1, 2, 3, 4],
        ]);

        static::assertTrue($map->contains());
        static::assertTrue($map->contains('a string'));
        static::assertTrue($map->contains('a string', 30));

        static::assertFalse($map->contains('b string'));
        static::assertFalse($map->contains('b string', 30));
    }

    public function testGetAndSet()
    {
        $map = new ArrayMap();

        static::assertNull($map->get('foo'));
        static::assertEquals('default', $map->get('foo', 'default'));

        $map->set('foo', 'foo string');

        static::assertEquals('foo string', $map->get('foo'));
        static::assertEquals('foo string', $map->get('foo', 'default'));

        $map->set('foo', null);

        static::assertNull($map->get('foo'));
        static::assertNull($map->get('foo', 'default'));
    }

    public function testHasAndRemove()
    {
        $map = new ArrayMap();

        static::assertFalse($map->has('foo'));
        static::assertFalse($map->has('foo', 'bar'));

        $map->set('foo', 'foo string');

        static::assertTrue($map->has('foo'));
        static::assertFalse($map->has('foo', 'bar'));

        $map->set('bar', 'bar string');

        static::assertTrue($map->has('foo'));
        static::assertTrue($map->has('foo', 'bar'));

        $map->set('bar', null);

        static::assertTrue($map->has('foo', 'bar'));

        $map->remove('foo', 'bar', 'unknown');

        static::assertTrue($map->has());
        static::assertFalse($map->has('foo'));
        static::assertFalse($map->has('bar'));
        static::assertFalse($map->has('foo', 'bar'));
    }
    
    public function testKeys()
    {
        $map = new ArrayMap([
            'a' => 'a string',
            'b' => 30,
            'c' => 30.33,
            'd' => null,
            'e' => new stdClass,
            'f' => [1, 2, 3, 4],
        ]);

        static::assertInstanceOf(ListInterface::class, $map->keys());
        static::assertEquals(['a', 'b', 'c', 'd', 'e', 'f', ], $map->keys()->toArray());
    }

    public function testValues()
    {
        $map = new ArrayMap([
            'a' => 'a string',
            'b' => 30,
            'c' => 30.33,
            'd' => null,
            'e' => new stdClass,
            'f' => [1, 2, 3, 4],
        ]);

        static::assertInstanceOf(ListInterface::class, $map->values());
        static::assertEquals([
            'a string',
            30,
            30.33,
            null,
            new stdClass,
            [1, 2, 3, 4],
        ], $map->values()->toArray());
    }
    
    public function testMap()
    {
        $map = new ArrayMap([
            'a' => 'a string',
            'b' => 'b string',
        ]);
        static::assertEquals([
            'a' => 'a string a',
            'b' => 'b string b',
        ], $map->map(function ($item, $key) {
            return $item . ' ' . $key;
        })->toArray());
    }

    public function testReduce()
    {
        $map = new ArrayMap([
            'a' => 'a string',
            'b' => 'b string',
        ]);
        static::assertEquals("a string a\nb string b\n", $map->reduce(function ($carry, $item, $key) {
            return $carry . $item . ' ' . $key . "\n";
        }, ''));
    }
}
