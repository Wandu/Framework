<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Stubs\AdminController;
use Wandu\Router\Stubs\AuthFailMiddleware;
use Wandu\Router\Stubs\AuthSuccessMiddleware;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testExecuteWithoutMiddlewares()
    {
        $route = new Route(AdminController::class, 'index');

        $mockRequest = Mockery::mock(ServerRequestInterface::class);

        $this->assertEquals('index@AdminController string', $route->execute($mockRequest));
    }

    public function testExecuteWithMiddleware()
    {
        $route = new Route(AdminController::class, 'index', [
            AuthSuccessMiddleware::class
        ]);

        $mockRequest = Mockery::mock(ServerRequestInterface::class);

        $this->assertEquals('auth[index@AdminController string]', $route->execute($mockRequest));
    }

    public function testExecuteWithPreventedMiddleware()
    {
        $route = new Route(AdminController::class, 'index', [
            AuthFailMiddleware::class
        ]);

        $mockRequest = Mockery::mock(ServerRequestInterface::class);

        $this->assertEquals('auth fail...', $route->execute($mockRequest));
    }
}
