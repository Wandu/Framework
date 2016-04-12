<?php
namespace Wandu\Router;

use Mockery;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Stubs\AdminController;
use Wandu\Router\Stubs\AuthFailMiddleware;
use Wandu\Router\Stubs\AuthSuccessMiddleware;

class RouteTest extends TestCase
{
    public function testExecuteWithoutMiddlewares()
    {
        $route = new Route(AdminController::class, 'index');

        $request = $this->createRequest('GET', '/');

        $this->assertEquals('[GET] index@Admin', $route->execute($request, new DefaultLoader()));
    }

    public function testExecuteWithMiddleware()
    {
        $route = new Route(AdminController::class, 'index', [
            AuthSuccessMiddleware::class
        ]);

        $request = $this->createRequest('GET', '/');

        $this->assertEquals('[GET] auth success; [GET] index@Admin', $route->execute($request, new DefaultLoader()));
    }

    public function testExecuteWithPreventedMiddleware()
    {
        $route = new Route(AdminController::class, 'index', [
            AuthFailMiddleware::class
        ]);

        $request = $this->createRequest('GET', '/');

        $this->assertEquals('[GET] auth fail;', $route->execute($request, new DefaultLoader()));
    }
}
