<?php
namespace Wandu\DI\Containee;

use PHPUnit\Framework\TestCase;
use stdClass;
use Wandu\Assertions;
use Wandu\DI\Container;

class FactoryTest extends TestCase
{
    use Assertions;

    public function testInstanceFactory()
    {
        $container = new Container();

        $object1 = new stdClass();
        $container->instance('obj1', $object1);

        // all same
        static::assertSame($object1, $container['obj1']);
        static::assertSame($object1, $container['obj1']);
        static::assertSame($object1, $container['obj1']);


        $object2 = new stdClass();
        $container->instance('obj2', $object2)->factory();

        // all not same
        $object2_1 = $container['obj2'];

        static::assertNotSame($object2, $object2_1);
        static::assertEquals($object2, $object2_1);

        $object2_2 = $container['obj2'];

        static::assertNotSame($object2, $object2_2);
        static::assertEquals($object2, $object2_2);
        static::assertNotSame($object2_1, $object2_2);
        static::assertEquals($object2_1, $object2_2);
    }

    public function testClosureFactory()
    {
        $container = new Container();

        $container->closure('obj1', function () {
            return new stdClass();
        });

        // all same
        $object1 = $container['obj1'];
        static::assertSame($object1, $container['obj1']);
        static::assertSame($object1, $container['obj1']);
        static::assertSame($object1, $container['obj1']);


        $container->closure('obj2', function () {
            return new stdClass();
        })->factory(true);
        $object2 = $container['obj2'];

        // all not same
        $object2_1 = $container['obj2'];

        static::assertNotSame($object2, $object2_1);
        static::assertEquals($object2, $object2_1);

        $object2_2 = $container['obj2'];

        static::assertNotSame($object2, $object2_2);
        static::assertEquals($object2, $object2_2);
        static::assertNotSame($object2_1, $object2_2);
        static::assertEquals($object2_1, $object2_2);
    }

    public function testBindFactory()
    {
        $container = new Container();

        $container->bind(FactoryTestIF::class, FactoryTestClass::class);

        // all same
        $object1 = $container[FactoryTestIF::class];
        static::assertSame($object1, $container[FactoryTestIF::class]);
        static::assertSame($object1, $container[FactoryTestIF::class]);
        static::assertSame($object1, $container[FactoryTestIF::class]);

        // reset
        $container = new Container();

        $container
            ->bind(FactoryTestIF::class, FactoryTestClass::class)
            ->factory(true);
        $object2 = $container[FactoryTestIF::class];

        // all not same
        $object2_1 = $container[FactoryTestIF::class];

        static::assertNotSame($object2, $object2_1);
        static::assertEquals($object2, $object2_1);

        $object2_2 = $container[FactoryTestIF::class];

        static::assertNotSame($object2, $object2_2);
        static::assertEquals($object2, $object2_2);
        static::assertNotSame($object2_1, $object2_2);
        static::assertEquals($object2_1, $object2_2);
    }
}

interface FactoryTestIF {}
class FactoryTestClass implements FactoryTestIF {}
