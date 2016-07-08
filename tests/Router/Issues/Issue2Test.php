<?php
namespace Wandu\Router\Issues;

use Mockery;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Dispatcher;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\Router;
use Wandu\Router\Contracts\RoutesInterface;
use Wandu\Router\Stubs\HomeController;
use Wandu\Router\TestCase;

class Issue2Test extends TestCase
{
    public function testDispatch()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRoutes(
            new class implements RoutesInterface
            {
                public function routes(Router $router)
                {
                    $router->createRoute(['GET'], '/', HomeController::class, 'wrong');
                }
            }
        );

        try {
            $dispatcher->dispatch($this->createRequest('GET', '/'));
            $this->fail();
        } catch (HandlerNotFoundException $exception) {
            $this->assertEquals(
                '"Wandu\Router\Stubs\HomeController::wrong" is not found.',
                $exception->getMessage()
            );
        }
    }
}
