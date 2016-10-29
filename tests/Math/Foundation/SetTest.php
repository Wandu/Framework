<?php
namespace Wandu\Math\Foundation;

use PHPUnit_Framework_TestCase;
use Wandu\Math\Foundation\Set\HashSet;
use stdClass;
use function Wandu\Math\Foundation\set;

class SetTest extends PHPUnit_Framework_TestCase
{
    public function testSetFunction()
    {
        $this->assertInstanceOf(HashSet::class, set());
    }

    public function testUniqueItems()
    {
        // scalar
        $this->assertEquals(3, count(set(1, 2, 3)));
        $this->assertEquals(3, count(set(1, 2, 3, 1, 2, 3)));

        // type strict
        $this->assertEquals(4, count(set(1, '1', 1.0, true)));
        $this->assertEquals(4, count(set(0, '', '0', false)));

        $obj1 = new stdClass();
        $obj2 = new stdClass();
        $obj3 = new stdClass();

        // object
        $this->assertEquals(3, count(set($obj1, $obj2, $obj3)));
        $this->assertEquals(3, count(set($obj1, $obj2, $obj3, $obj2, $obj3)));
        $this->assertEquals(2, count(set($obj2, $obj3, $obj2, $obj3)));

        // mix
        $this->assertEquals(6, count(set($obj1, $obj2, $obj3, 1, 2, 3)));
        $this->assertEquals(6, count(set($obj1, $obj2, $obj3, 1, 2, 3, $obj2, $obj3, 1, 3)));
    }

    public function testEqual()
    {
        $this->assertTrue(set(1, 2, 3)->equal(set(1, 2, 3)));

        // ordering
        $this->assertTrue(set(1, 2, 3)->equal(set(2, 3, 1)));

        $this->assertFalse(set('1', 2, 3)->equal(set(2, 3, 1)));
    }

    public function testHasAndRemove()
    {
        $set = set(1, 2, 3);

        $this->assertTrue($set->has(1));
        $this->assertTrue($set->has(2));
        $this->assertTrue($set->has(3));

        $this->assertFalse($set->has('1'));

        $set->remove('1');
        $this->assertTrue($set->has(1));

        $set->remove(1);
        $this->assertFalse($set->has(1));
    }

    public function testIteration()
    {
        $items = [1, 2, 3, '1', new stdClass()];
        $set = set(...$items);

        // equal
        $iteratedItems = [];
        foreach ($set as $item) {
            $iteratedItems[] = $item;
        }

        $this->assertTrue($set->equal(set(...$iteratedItems)));
    }
}
