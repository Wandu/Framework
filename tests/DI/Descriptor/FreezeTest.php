<?php
namespace Wandu\DI\Descriptor;

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

        $exception = static::catchException(function () use ($container) {
            $container->instance('obj2', new stdClass)->freeze();
            $container->destroy('obj2');
        });
        
        static::assertInstanceOf(CannotChangeException::class, $exception);
    }
}
