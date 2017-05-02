<?php
namespace Wandu\DI\Containnee;

use PHPUnit\Framework\TestCase;
use stdClass;
use Wandu\Assertions;
use Wandu\DI\Container;
use Wandu\DI\Exception\CannotChangeException;

class FreezeTest extends TestCase
{
    use Assertions;

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
}
