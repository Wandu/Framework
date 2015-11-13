<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\stubs\AdminController;
use Wandu\Router\Stubs\AuthFailMiddleware;
use Wandu\Router\Stubs\AuthSuccessMiddleware;
use Wandu\Router\Stubs\HomeController;

class RouterTest extends PHPUnit_Framework_TestCase
{
    /** @var Router */
    protected $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testDispatchDefault()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->once()->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->once()->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->once()->andReturn('/');

        $this->router->createRoute(['GET'], '/', AdminController::class, 'index');

        $this->assertEquals('index@Admin', $this->router->dispatch($mockRequest));
    }

    public function testDispatchMethod()
    {
        $mockGetRequest = Mockery::mock(ServerRequestInterface::class);
        $mockGetRequest->shouldReceive('getParsedBody')->once()->andReturn([]);
        $mockGetRequest->shouldReceive('getMethod')->once()->andReturn('GET');
        $mockGetRequest->shouldReceive('getUri->getPath')->once()->andReturn('/');

        $mockPostRequest = Mockery::mock(ServerRequestInterface::class);
        $mockPostRequest->shouldReceive('getParsedBody')->once()->andReturn([]);
        $mockPostRequest->shouldReceive('getMethod')->once()->andReturn('POST');
        $mockPostRequest->shouldReceive('getUri->getPath')->once()->andReturn('/');

        $this->router->createRoute(['GET'], '/', AdminController::class, 'index', [AuthSuccessMiddleware::class]);
        $this->router->createRoute(['POST'], '/', AdminController::class, 'index', [AuthFailMiddleware::class]);

        $this->assertEquals('auth[index@Admin]', $this->router->dispatch($mockGetRequest));

        $this->assertEquals('auth fail...', $this->router->dispatch($mockPostRequest));
    }

    public function testDispatchMatchingUri()
    {
        $getMock = Mockery::mock(ServerRequestInterface::class);
        $getMock->shouldReceive('getParsedBody')->once()->andReturn([]);
        $getMock->shouldReceive('getMethod')->once()->andReturn('GET');
        $getMock->shouldReceive('getUri->getPath')->once()->andReturn('/admin/index');

        $this->router->createRoute(['GET'], '/admin/index', AdminController::class, 'index');
        $this->router->createRoute(['GET'], '/admin/action', AdminController::class, 'action');

        $this->assertEquals('index@Admin', $this->router->dispatch($getMock));
    }

    public function testDispatchMatchingRegExpUri()
    {
        $getMock = Mockery::mock(ServerRequestInterface::class);
        $getMock->shouldReceive('getParsedBody')->once()->andReturn([]);
        $getMock->shouldReceive('getMethod')->once()->andReturn('GET');
        $getMock->shouldReceive('getUri->getPath')->once()->andReturn('/admin/doit/hello');

        $getMock->shouldReceive('withAttribute')->once()->with('action', 'hello')->andReturn($getMock);
        $getMock->shouldReceive('getAttribute')->once()->with('action')->andReturn('hello');

        $this->router->createRoute(['GET', 'POST'], '/admin/doit/{action}', AdminController::class, 'doit');

        $this->assertEquals('doit@Admin, hello', $this->router->dispatch($getMock));
    }

    public function testGroup()
    {
        $this->router->createRoute(['GET'], '/', HomeController::class, 'index');
        $this->router->group([
            'prefix' => '/admin',
            'middleware' => [AuthSuccessMiddleware::class],
        ], function (Router $router) {
            $router->createRoute(['GET'], '/', AdminController::class, 'index');
            $router->createRoute(['GET'], '/action', AdminController::class, 'action');
            $router->createRoute(['GET'], '/doit/{action}', AdminController::class, 'doit');
        });

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/');

        $this->assertEquals('index@Home', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/admin');

        $this->assertEquals('auth[index@Admin]', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/admin/action');

        $this->assertEquals('auth[action@Admin]', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/admin/doit/chu');

        $mockRequest->shouldReceive('withAttribute')->once()->with('action', 'chu')->andReturn($mockRequest);
        $mockRequest->shouldReceive('getAttribute')->once()->with('action')->andReturn('chu');

        $this->assertEquals('auth[doit@Admin, chu]', $this->router->dispatch($mockRequest));
    }

    public function testPrefix()
    {
        $this->router->createRoute(['GET'], '/', HomeController::class, 'index');
        $this->router->prefix('/admin', function (Router $router) {
            $router->createRoute(['GET'], '/', AdminController::class, 'index');
            $router->createRoute(['GET'], '/action', AdminController::class, 'action');
            $router->createRoute(['GET'], '/doit/{action}', AdminController::class, 'doit');
        });

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/');

        $this->assertEquals('index@Home', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/admin');

        $this->assertEquals('index@Admin', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/admin/action');

        $this->assertEquals('action@Admin', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/admin/doit/chu');

        $mockRequest->shouldReceive('withAttribute')->once()->with('action', 'chu')->andReturn($mockRequest);
        $mockRequest->shouldReceive('getAttribute')->once()->with('action')->andReturn('chu');

        $this->assertEquals('doit@Admin, chu', $this->router->dispatch($mockRequest));
    }

    public function testMiddlewares()
    {
        $this->router->middlewares([AuthSuccessMiddleware::class], function (Router $router) {
            $router->createRoute(['GET'], '/', AdminController::class, 'index');
            $router->createRoute(['GET'], '/action', AdminController::class, 'action');
            $router->createRoute(['GET'], '/doit/{action}', AdminController::class, 'doit');
        });

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/');

        $this->assertEquals('auth[index@Admin]', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/action');

        $this->assertEquals('auth[action@Admin]', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/doit/chu');

        $mockRequest->shouldReceive('withAttribute')->once()->with('action', 'chu')->andReturn($mockRequest);
        $mockRequest->shouldReceive('getAttribute')->once()->with('action')->andReturn('chu');

        $this->assertEquals('auth[doit@Admin, chu]', $this->router->dispatch($mockRequest));
    }

    public function testVirtualMethod()
    {
        $this->router->createRoute(['PUT'], '', AdminController::class, 'index');

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/');
        $mockRequest->shouldReceive('getMethod')->andReturn('POST');
        $mockRequest->shouldReceive('getParsedBody')->andReturn([
            '_method' => 'put'
        ]);

        $this->assertEquals('index@Admin', $this->router->dispatch($mockRequest));
    }
}
