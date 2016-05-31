<?php
namespace Wandu\DI;

use Mockery;
use stdClass;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Stub\Resolve\AutoResolvedDepend;
use Wandu\DI\Stub\Resolve\DependInterface;

class ContainerChainTest extends TestCase
{
    public function testFreeze()
    {
        $this->container->instance('obj1', new stdClass);
        $this->container->destroy('obj1');
        
        try {
            $this->container->instance('obj2', new stdClass)->freeze();
            $this->container->destroy('obj2');
            $this->fail();
        } catch (CannotChangeException $e) {
        }
    }

    public function testInstanceFactory()
    {
        $object1 = new stdClass();
        $this->container->instance('obj1', $object1);
        
        // all same
        $this->assertSame($object1, $this->container['obj1']);
        $this->assertSame($object1, $this->container['obj1']);
        $this->assertSame($object1, $this->container['obj1']);


        $object2 = new stdClass();
        $this->container->instance('obj2', $object2)->factory();

        // all not same
        $object2_1 = $this->container['obj2'];

        $this->assertNotSame($object2, $object2_1);
        $this->assertEquals($object2, $object2_1);
        
        $object2_2 = $this->container['obj2'];

        $this->assertNotSame($object2, $object2_2);
        $this->assertEquals($object2, $object2_2);
        $this->assertNotSame($object2_1, $object2_2);
        $this->assertEquals($object2_1, $object2_2);
    }

    public function testClosureFactory()
    {
        $this->container->closure('obj1', function () {
            return new stdClass();
        });

        // all same
        $object1 = $this->container['obj1'];
        $this->assertSame($object1, $this->container['obj1']);
        $this->assertSame($object1, $this->container['obj1']);
        $this->assertSame($object1, $this->container['obj1']);


        $this->container->closure('obj2', function () {
            return new stdClass();
        })->factory(true);
        $object2 = $this->container['obj2'];

        // all not same
        $object2_1 = $this->container['obj2'];

        $this->assertNotSame($object2, $object2_1);
        $this->assertEquals($object2, $object2_1);

        $object2_2 = $this->container['obj2'];

        $this->assertNotSame($object2, $object2_2);
        $this->assertEquals($object2, $object2_2);
        $this->assertNotSame($object2_1, $object2_2);
        $this->assertEquals($object2_1, $object2_2);
    }

    public function testBindFactory()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);

        // all same
        $object1 = $this->container[DependInterface::class];
        $this->assertSame($object1, $this->container[DependInterface::class]);
        $this->assertSame($object1, $this->container[DependInterface::class]);
        $this->assertSame($object1, $this->container[DependInterface::class]);

        $this->container = new Container();
        
        $this->container
            ->bind(DependInterface::class, AutoResolvedDepend::class)
            ->factory(true);
        $object2 = $this->container[DependInterface::class];

        // all not same
        $object2_1 = $this->container[DependInterface::class];

        $this->assertNotSame($object2, $object2_1);
        $this->assertEquals($object2, $object2_1);

        $object2_2 = $this->container[DependInterface::class];

        $this->assertNotSame($object2, $object2_2);
        $this->assertEquals($object2, $object2_2);
        $this->assertNotSame($object2_1, $object2_2);
        $this->assertEquals($object2_1, $object2_2);
    }
}
