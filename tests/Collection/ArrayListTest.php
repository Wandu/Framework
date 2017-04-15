<?php
namespace Wandu\Collection;

use stdClass;
use PHPUnit_Framework_TestCase;
use Wandu\Assertions;
use InvalidArgumentException;
use Wandu\Collection\Contracts\ListInterface;
use Wandu\Collection\Contracts\MapInterface;

class ArrayListTest extends PHPUnit_Framework_TestCase
{
    use Assertions;

    public function provideList()
    {
        $gen = function () {
            foreach (range(1, 5) as $index) {
                yield $index;
            }
        };
        return [
            [new ArrayList([1, 2, 3, 4, 5,])],
            [new ArrayList($gen())],
        ];
    }

    public function provideListByList()
    {
        $gen1 = function () {
            foreach (range(1, 5) as $index) {
                yield $index;
            }
        };
        $gen2 = function () {
            foreach (range(4, 8) as $index) {
                yield $index;
            }
        };
        return [
            [new ArrayList([1, 2, 3, 4, 5,]), new ArrayList([4, 5, 6, 7, 8]),],
            [new ArrayList([1, 2, 3, 4, 5,]), new ArrayList($gen2()),],
            [new ArrayList($gen1()), new ArrayList([4, 5, 6, 7, 8]),],
            [new ArrayList($gen1()), new ArrayList($gen2())],
        ];
    }

    public function testGenerator()
    {
        $expected = <<<TEXT
generator start
generator end
loop start
0 : 0
1 : 1
2 : 4
3 : 9
loop start
0 : 0
1 : 1
2 : 4
3 : 9

TEXT;
        static::assertOutputBufferEquals($expected, function () {
            $gen = function () {
                echo "generator start\n";
                foreach (range(0, 3) as $index) {
                    yield $index * $index;
                }
                echo "generator end\n";
            };

            $list = new ArrayList($gen());

            echo "loop start\n";
            foreach ($list as $index => $value) {
                echo "{$index} : {$value}\n";
            }
            echo "loop start\n";
            foreach ($list as $index => $value) {
                echo "{$index} : {$value}\n";
            }
        });
    }
    
    public function testToString()
    {
        $list = new ArrayList([
            'a string',
            30,
            30.33,
            null,
            new stdClass,
            [1, 2, 3, 4],
        ]);
        $expected = <<<TEXT
Wandu\Collection\ArrayList [
    "a string",
    30,
    30.33,
    null,
    [stdClass],
    [array],
]
TEXT;

        static::assertEquals($expected, $list->__toString());
    }

    public function testToArrayAndAll()
    {
        $list = new ArrayList([
            new ArrayMap(['name' => 'wan2land']),
            new ArrayMap(['name' => 'wan3land']),
            new ArrayMap(['name' => 'wan4land']),
            new ArrayMap(['name' => 'wan5land']),
        ]);
        
        static::assertEquals([
            new ArrayMap(['name' => 'wan2land']),
            new ArrayMap(['name' => 'wan3land']),
            new ArrayMap(['name' => 'wan4land']),
            new ArrayMap(['name' => 'wan5land']),
        ], $list->all());

        static::assertEquals([
            ['name' => 'wan2land'],
            ['name' => 'wan3land'],
            ['name' => 'wan4land'],
            ['name' => 'wan5land'],
        ], $list->toArray());
    }

    public function testJsonSerialize()
    {
        $list = new ArrayList([
            'a string',
            30,
            30.33,
            null,
            new stdClass,
            [1, 2, 3, 4],
        ]);

        static::assertEquals('["a string",30,30.33,null,{},[1,2,3,4]]', json_encode($list));

        $list = new ArrayList([
            new ArrayMap(['name' => 'wan2land']),
            new ArrayMap(['name' => 'wan3land']),
            new ArrayMap(['name' => 'wan4land']),
            new ArrayMap(['name' => 'wan5land']),
        ]);
        
        static::assertEquals('[{"name":"wan2land"},{"name":"wan3land"},{"name":"wan4land"},{"name":"wan5land"}]', json_encode($list));
    }
    
    public function testCount()
    {
        $list = new ArrayList([1, 2, 3, 4, 5]);
        static::assertSame(5, count($list));

        $gen = function () {
            foreach (range(0, 5) as $item) {
                yield $item;
            }
        };
        $list = new ArrayList($gen());
        static::assertSame(6, count($list));
    }

    public function testOffsetGetAndSet()
    {
        $list = new ArrayList();

        $handleListWithString = function () use ($list) {
            $list['foo'] = 'foo string';
        };

        static::assertExceptionInstanceOf(InvalidArgumentException::class, $handleListWithString);
        static::assertExceptionMessageEquals('Argument 1 passed to Wandu\Collection\ArrayList::offsetSet must be null or an integer less than the size of the list',
            $handleListWithString);

        static::assertNull($list['foo']);

        $list[] = 11;
        $list[] = 22;
        $list[] = 33;

        static::assertEquals(3, count($list));
        static::assertEquals(11, $list[0]);
        static::assertEquals(22, $list[1]);
        static::assertEquals(33, $list[2]);

        $list[1] = 111;
        static::assertEquals(3, count($list));
        static::assertEquals(111, $list[1]);

        $list['2'] = 222;
        static::assertEquals(3, count($list));
        static::assertEquals(222, $list[2]);

        $list[3] = 333;
        static::assertEquals(4, count($list));
        static::assertEquals(333, $list[3]);

        static::assertNull($list[10]); // default is null

        $handleListWithBigInteger = function () use ($list) {
            $list[5] = 555;
        };

        static::assertExceptionInstanceOf(InvalidArgumentException::class, $handleListWithBigInteger);
        static::assertExceptionMessageEquals('Argument 1 passed to Wandu\Collection\ArrayList::offsetSet must be null or an integer less than the size of the list',
            $handleListWithBigInteger);
    }

    public function testOffsetExistsAndUnset()
    {
        $list = new ArrayList();

        static::assertFalse(isset($list['foo']));

        $list[] = 0;
        $list[] = 1;
        $list[] = 2;

        static::assertEquals(3, count($list));
        static::assertTrue(isset($list[0]));

        unset($list[1]);

        static::assertEquals(2, count($list));
        static::assertTrue(isset($list[0]));
        static::assertTrue(isset($list[1]));
        static::assertFalse(isset($list[2]));

        static::assertEquals(0, $list[0]);
        static::assertEquals(2, $list[1]);
    }

    public function testSerialize()
    {
        $list = new ArrayList([
            'a string',
            30,
            30.33,
            null,
            new stdClass,
            [1, 2, 3, 4],
        ]);

        $serializedMap = unserialize(serialize($list));
        static::assertEquals($serializedMap, $list);
    }
    
    public function testContains()
    {
        $list = new ArrayList([
            'a string',
            30,
            30.33,
            null,
            new stdClass,
            [1, 2, 3, 4],
        ]);

        static::assertTrue($list->contains());
        static::assertTrue($list->contains('a string'));
        static::assertTrue($list->contains('a string', 30));

        static::assertFalse($list->contains('b string'));
        static::assertFalse($list->contains('b string', 30));
    }

    public function testGetAndSet()
    {
        $list = new ArrayList();

        static::assertNull($list->get(0));
        static::assertEquals('default', $list->get(0, 'default'));

        $list->set(0, 'foo string');

        static::assertEquals('foo string', $list->get(0));
        static::assertEquals('foo string', $list->get(0, 'default'));

        $list->set(0, null);

        static::assertNull($list->get(0));
        static::assertNull($list->get(0, 'default'));

    }

    public function testHasAndRemove()
    {
        $list = new ArrayList();

        static::assertFalse($list->has(0));
        static::assertFalse($list->has(0, 'bar'));

        $list->set(0, 'foo string');

        static::assertTrue($list->has(0));
        static::assertFalse($list->has(0, 1));

        $list->set(1, 'bar string');

        static::assertTrue($list->has(0));
        static::assertTrue($list->has(0, 1));

        $list->set('get', null);
        $list->set(1, null);

        static::assertTrue($list->has(0, 1));

        $list->remove(0, 1, 'unknown');

        static::assertTrue($list->has());
        static::assertFalse($list->has(0));
        static::assertFalse($list->has(1));
        static::assertFalse($list->has(0, 1));
    }

    public function testFilter()
    {
        $list = new ArrayList([1, 2, null, 4, 5]);
        static::assertSame([1, 2, 4, 5,], $list->filter()->toArray());

        if (defined('HHVM_VERSION')) {
            $list = new ArrayList([1, 2, null, 4, 5]);
            static::assertSame([4, 5,], $list->filter(function ($item) {
                return $item > 3;
            })->toArray());
        } else {
            $list = new ArrayList([1, 2, null, 4, 5]);
            static::assertSame([4, 5,], $list->filter(function ($item, $key) {
                return $item > 3;
            })->toArray());

            $list = new ArrayList([1, 2, null, 4, 5]);
            static::assertSame([1, 2,], $list->filter(function ($item, $key) {
                return $key < 2;
            })->toArray());
        }
    }

    public function testMap()
    {
        $list = new ArrayList([1, 2, 3, 4, 5]);
        static::assertSame([
            ['key' => '0: 1'],
            ['key' => '1: 2'],
            ['key' => '2: 3'],
            ['key' => '3: 4'],
            ['key' => '4: 5'],
        ], $list->map(function ($item, $key) {
            return ['key' => $key . ': ' . $item];
        })->toArray());
    }

    public function testReduce()
    {
        $list = new ArrayList([1, 2, 3, 4, 5]);
        static::assertSame(15, $list->reduce(function ($carry, $item) {
            return $carry + $item;
        }, 0));
        static::assertSame(15, $list->reduce(function ($carry, $item) {
            return $carry + $item;
        }));

        static::assertSame(10, $list->reduce(function ($carry, $item, $key) {
            return $carry + $key;
        }, 0));
        static::assertSame(10, $list->reduce(function ($carry, $item, $key) {
            return $carry + $key;
        }));
    }

    /**
     * @dataProvider provideList
     * @param \Wandu\Collection\ArrayList $list
     */
    public function testGroupBy(ArrayList $list)
    {
        $groupedMap = $list->groupBy(function ($item, $key) {
            return $item % 2;
        });

        static::assertInstanceOf(MapInterface::class, $groupedMap);
        static::assertInstanceOf(ListInterface::class, $groupedMap[0]);
        static::assertInstanceOf(ListInterface::class, $groupedMap[1]);

        static::assertEquals([2, 4,], $groupedMap[0]->toArray());
        static::assertEquals([1, 3, 5,], $groupedMap[1]->toArray());
    }

    /**
     * @dataProvider provideList
     * @param \Wandu\Collection\ArrayList $list
     */
    public function testKeyBy(ArrayList $list)
    {
        $keyByMap = $list->keyBy(function ($item, $key) {
            return 'item-' . $item . '-' . $key;
        });

        static::assertInstanceOf(MapInterface::class, $keyByMap);
        static::assertEquals([
            'item-1-0' => 1,
            'item-2-1' => 2,
            'item-3-2' => 3,
            'item-4-3' => 4,
            'item-5-4' => 5,
        ], $keyByMap->toArray());
    }

    public function testPushAndPop()
    {
        $list = new ArrayList();

        $list->push(8);
        $list->push(1)->push(7);
        $list->push(3, 5);

        static::assertEquals([8, 1, 7, 3, 5,], $list->toArray());

        static::assertEquals(5, $list->pop());
        static::assertEquals(3, $list->pop());
        static::assertEquals(7, $list->pop());

        static::assertEquals([8, 1,], $list->toArray());

        static::assertEquals(1, $list->pop());
        static::assertEquals(8, $list->pop());
        static::assertNull($list->pop());
    }

    public function testShiftAndUnshift()
    {
        $list = new ArrayList();

        $list->unshift(8);
        $list->unshift(1)->unshift(7);
        $list->unshift(3, 5);

        static::assertEquals([5, 3, 7, 1, 8,], $list->toArray());

        static::assertEquals(5, $list->shift());
        static::assertEquals(3, $list->shift());
        static::assertEquals(7, $list->shift());

        static::assertEquals([1, 8,], $list->toArray());

        static::assertEquals(1, $list->shift());
        static::assertEquals(8, $list->shift());
        static::assertNull($list->shift());
    }

    /**
     * @dataProvider provideList
     * @param ArrayList $list
     */
    public function testReverse(ArrayList $list)
    {
        $reversedList = $list->reverse();

        static::assertNotSame($list, $reversedList);
        static::assertEquals([5, 4, 3, 2, 1,], $reversedList->toArray());
    }

    /**
     * @dataProvider provideList
     * @param ArrayList $list
     */
    public function testShuffle(ArrayList $list)
    {
        $count = 0;
        $shuffledList = $list;
        do {
            $shuffledList = $shuffledList->shuffle();
            if ($count++ > 10) {
                static::fail();
            }
        } while ($shuffledList->toArray() == [1, 2, 3, 4, 5,]);

        static::assertEquals(5, count($shuffledList));
        static::assertNotSame($shuffledList, $list);
        static::assertTrue($shuffledList->contains(1, 2, 3, 4, 5));
    }

    public function testSort()
    {
        $list = new ArrayList([2, 3, 1, 4, 5,]);
        $sortedList = $list->sort();
        static::assertNotSame($list, $sortedList);
        static::assertEquals([1, 2, 3, 4, 5,], $sortedList->toArray());

        $sortedList = $list->sort(function ($x, $y) {
            return $x < $y ? 1 : -1;
        });
        static::assertNotSame($list, $sortedList);
        static::assertEquals([5, 4, 3, 2, 1,], $sortedList->toArray());
    }

    /**
     * @dataProvider provideList
     * @param ArrayList $list
     */
    public function testSlice(ArrayList $list)
    {
        $slicedList = $list->slice(1);
        static::assertNotSame($list, $slicedList);
        static::assertEquals([2, 3, 4, 5,], $slicedList->toArray());

        $slicedList = $list->slice(1, 3);
        static::assertNotSame($list, $slicedList);
        static::assertEquals([2, 3, 4,], $slicedList->toArray());
    }

    /**
     * @dataProvider provideList
     * @param ArrayList $list
     */
    public function testSpliceOnlyOffset(ArrayList $list)
    {
        $splicedList = $list->splice(2);
        static::assertNotSame($list, $splicedList);
        static::assertEquals([1, 2,], $list->toArray());
        static::assertEquals([3, 4, 5,], $splicedList->toArray());
    }

    /**
     * @dataProvider provideList
     * @param ArrayList $list
     */
    public function testSpliceWithLength(ArrayList $list)
    {
        $splicedList = $list->splice(1, 2);
        static::assertNotSame($list, $splicedList);
        static::assertEquals([1, 4, 5,], $list->toArray());
        static::assertEquals([2, 3,], $splicedList->toArray());
    }

    public function testUnique()
    {
        $list = new ArrayList([1, 1, 1, 2, 2, 3]);
        $uniqueList = $list->unique();
        static::assertNotSame($list, $uniqueList);
        static::assertEquals([1, 2, 3,], $uniqueList->toArray());
    }

    /**
     * @dataProvider provideListByList
     * @param \Wandu\Collection\ArrayList $list1
     * @param \Wandu\Collection\ArrayList $list2
     */
    public function testMerge(ArrayList $list1, ArrayList $list2)
    {
        $mergedList = $list1->merge($list2);

        static::assertNotSame($mergedList, $list1);
        static::assertNotSame($mergedList, $list2);

        static::assertEquals([1, 2, 3, 4, 5,], $list1->toArray());
        static::assertEquals([4, 5, 6, 7, 8,], $list2->toArray());
        static::assertEquals([1, 2, 3, 4, 5, 4, 5, 6, 7, 8,], $mergedList->toArray());
    }

    /**
     * @dataProvider provideList
     * @param ArrayList $list
     */
    public function testImplode(ArrayList $list)
    {
        static::assertEquals('1,2,3,4,5', $list->implode(','));
    }

    /**
     * @dataProvider provideList
     * @param ArrayList $list
     */
    public function testIsEmpty(ArrayList $list)
    {
        static::assertFalse($list->isEmpty());

        $list->pop();
        $list->pop();
        $list->pop();
        $list->pop();
        $list->pop();

        static::assertTrue($list->isEmpty());
    }

    /**
     * @dataProvider provideListByList
     * @param \Wandu\Collection\ArrayList $list1
     * @param \Wandu\Collection\ArrayList $list2
     */
    public function testCombine(ArrayList $list1, ArrayList $list2)
    {
        $combinedList = $list1->combine($list2);

        static::assertInstanceOf(MapInterface::class, $combinedList);

        static::assertNotSame($combinedList, $list1);
        static::assertNotSame($combinedList, $list2);

        static::assertEquals([1, 2, 3, 4, 5,], $list1->toArray());
        static::assertEquals([4, 5, 6, 7, 8,], $list2->toArray());

        static::assertEquals([
            1 => 4,
            2 => 5,
            3 => 6,
            4 => 7,
            5 => 8,
        ], $combinedList->toArray());
    }

    /**
     * @dataProvider provideListByList
     * @param \Wandu\Collection\ArrayList $list1
     * @param \Wandu\Collection\ArrayList $list2
     */
    public function testIntersect(ArrayList $list1, ArrayList $list2)
    {
        $intersectedList = $list1->intersect($list2);

        static::assertInstanceOf(ListInterface::class, $intersectedList);

        static::assertNotSame($intersectedList, $list1);
        static::assertNotSame($intersectedList, $list2);

        static::assertEquals([1, 2, 3, 4, 5,], $list1->toArray());
        static::assertEquals([4, 5, 6, 7, 8,], $list2->toArray());

        static::assertEquals([4, 5, ], $intersectedList->toArray());
    }

    /**
     * @dataProvider provideListByList
     * @param \Wandu\Collection\ArrayList $list1
     * @param \Wandu\Collection\ArrayList $list2
     */
    public function testDiff(ArrayList $list1, ArrayList $list2)
    {
        $diffedList = $list1->intersect($list2);

        static::assertInstanceOf(ListInterface::class, $diffedList);

        static::assertNotSame($diffedList, $list1);
        static::assertNotSame($diffedList, $list2);

        static::assertEquals([1, 2, 3, 4, 5,], $list1->toArray());
        static::assertEquals([4, 5, 6, 7, 8,], $list2->toArray());

        static::assertEquals([4, 5,], $diffedList->toArray());
    }

    /**
     * @dataProvider provideListByList
     * @param \Wandu\Collection\ArrayList $list1
     * @param \Wandu\Collection\ArrayList $list2
     */
    public function testUnion(ArrayList $list1, ArrayList $list2)
    {
        $unionList = $list1->union($list2);

        static::assertInstanceOf(ListInterface::class, $unionList);

        static::assertNotSame($unionList, $list1);
        static::assertNotSame($unionList, $list2);

        static::assertEquals([1, 2, 3, 4, 5,], $list1->toArray());
        static::assertEquals([4, 5, 6, 7, 8,], $list2->toArray());

        static::assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $unionList->toArray());
    }

    /**
     * @dataProvider provideList
     * @param \Wandu\Collection\ArrayList $list
     */
    public function testFirst(ArrayList $list)
    {
        static::assertEquals(1, $list->first());
        static::assertEquals(4, $list->first(function ($item, $key) {
            return $item > 3;
        }));
        static::assertEquals(5, $list->first(function ($item, $key) {
            return $key > 3;
        }));
        static::assertNull($list->first(function ($item, $key) {
            return $key > 99;
        }));
        static::assertEquals('default value', $list->first(function ($item, $key) {
            return $key > 99;
        }, 'default value'));
    }

    /**
     * @dataProvider provideList
     * @param \Wandu\Collection\ArrayList $list
     */
    public function testLast(ArrayList $list)
    {
        static::assertEquals(5, $list->last());
        static::assertEquals(2, $list->last(function ($item, $key) {
            return $item < 3;
        }));
        static::assertEquals(3, $list->last(function ($item, $key) {
            return $key < 3;
        }));
        static::assertNull($list->last(function ($item, $key) {
            return $key > 99;
        }));
        static::assertEquals('default value', $list->last(function ($item, $key) {
            return $key > 99;
        }, 'default value'));
    }
}
