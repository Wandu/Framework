<?php
namespace Wandu\Router;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testSimple()
    {
        $router = new Router;

        $router->createRoute(['GET'], '/admin', "TestSimpleController", 'index');
        $router->createRoute(['POST', 'PUT'], '/admin', "TestSimpleController", 'action');

        static::assertEquals(
            [
                [['GET'], '/admin', new Route("TestSimpleController", "index")],
                [['POST', 'PUT'], '/admin', new Route("TestSimpleController", "action")],
            ],
            iterator_to_array($router)
        );
    }

    public function testGroup()
    {
        $router = new Router;

        $router->createRoute(['GET'], '/', "TestGroupController", 'index');
        $router->group([
            'prefix' => 'admin',
            'middleware' => ["TestGroupMiddleware"],
        ], function (Router $router) {
            $router->createRoute(['GET'], '', "TestGroupController2", 'index');
            $router->createRoute(['POST'], '/', "TestGroupController2", 'store');
            $router->createRoute(['GET'], 'users/:user', "TestGroupController2", 'show');
        });

        static::assertEquals(
            [
                [['GET'], '/', new Route("TestGroupController", "index")],
                [['GET'], '/admin', new Route("TestGroupController2", "index", ["TestGroupMiddleware"])],
                [['POST'], '/admin', new Route("TestGroupController2", "store", ["TestGroupMiddleware"])],
                [['GET'], '/admin/users/:user', new Route("TestGroupController2", "show", ["TestGroupMiddleware"])],
            ],
            iterator_to_array($router)
        );
    }
}
