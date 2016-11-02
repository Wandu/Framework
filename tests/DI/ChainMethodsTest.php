<?php
namespace Wandu\DI;

use stdClass;
use Wandu\DI\Exception\CannotChangeException;
use PHPUnit_Framework_TestCase;

class ChainMethodsTest extends PHPUnit_Framework_TestCase
{
    public function testFreeze()
    {
        $container = new Container();
        
        $container->instance('obj1', new stdClass);
        $container->destroy('obj1');
        
        try {
            $container->instance('obj2', new stdClass)->freeze();
            $container->destroy('obj2');
            static::fail();
        } catch (CannotChangeException $e) {
        }
    }

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
        
        $container->bind(ChainMethodTestDependInterface::class, ChainMethodTestDepend::class);

        // all same
        $object1 = $container[ChainMethodTestDependInterface::class];
        static::assertSame($object1, $container[ChainMethodTestDependInterface::class]);
        static::assertSame($object1, $container[ChainMethodTestDependInterface::class]);
        static::assertSame($object1, $container[ChainMethodTestDependInterface::class]);

        // reset
        $container = new Container();
        
        $container
            ->bind(ChainMethodTestDependInterface::class, ChainMethodTestDepend::class)
            ->factory(true);
        $object2 = $container[ChainMethodTestDependInterface::class];

        // all not same
        $object2_1 = $container[ChainMethodTestDependInterface::class];

        static::assertNotSame($object2, $object2_1);
        static::assertEquals($object2, $object2_1);

        $object2_2 = $container[ChainMethodTestDependInterface::class];

        static::assertNotSame($object2, $object2_2);
        static::assertEquals($object2, $object2_2);
        static::assertNotSame($object2_1, $object2_2);
        static::assertEquals($object2_1, $object2_2);
    }
}

interface ChainMethodTestDependInterface {}
class ChainMethodTestDepend implements ChainMethodTestDependInterface {}
