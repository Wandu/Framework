<?php
namespace Wandu\DI;

use stdClass;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Stub\Resolve\AutoResolvedDepend;
use Wandu\DI\Stub\Resolve\DependInterface;
use PHPUnit_Framework_TestCase;

class ContainerChainTest extends PHPUnit_Framework_TestCase
{
    public function testFreeze()
    {
        $container = new Container();
        
        $container->instance('obj1', new stdClass);
        $container->destroy('obj1');
        
        try {
            $container->instance('obj2', new stdClass)->freeze();
            $container->destroy('obj2');
            $this->fail();
        } catch (CannotChangeException $e) {
        }
    }

    public function testInstanceFactory()
    {
        $container = new Container();

        $object1 = new stdClass();
        $container->instance('obj1', $object1);
        
        // all same
        $this->assertSame($object1, $container['obj1']);
        $this->assertSame($object1, $container['obj1']);
        $this->assertSame($object1, $container['obj1']);


        $object2 = new stdClass();
        $container->instance('obj2', $object2)->factory();

        // all not same
        $object2_1 = $container['obj2'];

        $this->assertNotSame($object2, $object2_1);
        $this->assertEquals($object2, $object2_1);
        
        $object2_2 = $container['obj2'];

        $this->assertNotSame($object2, $object2_2);
        $this->assertEquals($object2, $object2_2);
        $this->assertNotSame($object2_1, $object2_2);
        $this->assertEquals($object2_1, $object2_2);
    }

    public function testClosureFactory()
    {
        $container = new Container();

        $container->closure('obj1', function () {
            return new stdClass();
        });

        // all same
        $object1 = $container['obj1'];
        $this->assertSame($object1, $container['obj1']);
        $this->assertSame($object1, $container['obj1']);
        $this->assertSame($object1, $container['obj1']);


        $container->closure('obj2', function () {
            return new stdClass();
        })->factory(true);
        $object2 = $container['obj2'];

        // all not same
        $object2_1 = $container['obj2'];

        $this->assertNotSame($object2, $object2_1);
        $this->assertEquals($object2, $object2_1);

        $object2_2 = $container['obj2'];

        $this->assertNotSame($object2, $object2_2);
        $this->assertEquals($object2, $object2_2);
        $this->assertNotSame($object2_1, $object2_2);
        $this->assertEquals($object2_1, $object2_2);
    }

    public function testBindFactory()
    {
        $container = new Container();
        
        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        // all same
        $object1 = $container[DependInterface::class];
        $this->assertSame($object1, $container[DependInterface::class]);
        $this->assertSame($object1, $container[DependInterface::class]);
        $this->assertSame($object1, $container[DependInterface::class]);

        // reset
        $container = new Container();
        
        $container
            ->bind(DependInterface::class, AutoResolvedDepend::class)
            ->factory(true);
        $object2 = $container[DependInterface::class];

        // all not same
        $object2_1 = $container[DependInterface::class];

        $this->assertNotSame($object2, $object2_1);
        $this->assertEquals($object2, $object2_1);

        $object2_2 = $container[DependInterface::class];

        $this->assertNotSame($object2, $object2_2);
        $this->assertEquals($object2, $object2_2);
        $this->assertNotSame($object2_1, $object2_2);
        $this->assertEquals($object2_1, $object2_2);
    }
}
