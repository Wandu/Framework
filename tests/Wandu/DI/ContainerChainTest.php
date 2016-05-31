<?php
namespace Wandu\DI;

use Mockery;
use stdClass;
use Wandu\DI\Exception\CannotChangeException;

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

    public function testAlias()
    {
//        $object1 = new stdClass();
//        $this->container->instance('obj1', $object1);
//        
//        // same
//        $this->assertSame($object1, $this->container['alias1']);
    }
}
