<?php
namespace Wandu\Router;

use Mockery;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Stubs\AdminController;
use Wandu\Router\Stubs\AuthSuccessMiddleware;
use Wandu\Router\Stubs\HomeController;

class DispatcherTest extends TestCase
{
    public function testSimpleDispatcher()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRouter(function (Router $router) {
            $router->createRoute(['GET'], '/', HomeController::class);
        });

        $this->assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))
        );
    }

    public function testDispatchMethod()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRouter(function (Router $router) {
            $router->createRoute(['GET'], '/admin', AdminController::class, 'index');
            $router->createRoute(['POST'], '/admin', AdminController::class, 'action');
        });

        $this->assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))
        );
        $this->assertEquals(
            '[POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))
        );

        foreach (['PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $dispatcher->dispatch($this->createRequest($method, '/admin'));
                $this->fail();
            } catch (MethodNotAllowedException $exception) {
            }
        }
    }

    public function testDispatchMatchingUri()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRouter(function (Router $router) {
            $router->createRoute(['GET'], '/admin/index', AdminController::class, 'index');
            $router->createRoute(['GET'], '/admin/action', AdminController::class, 'action');
        });

        $this->assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin/index'))
        );
        $this->assertEquals(
            '[GET] action@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin/action'))
        );
    }

    public function testDispatchMatchingRegExpUri()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRouter(function (Router $router) {
            $router->createRoute(['GET'], '/admin/users/{user}', AdminController::class, 'users');
        });

        $request = $this->createRequest('GET', '/admin/users/37');
        $request->shouldReceive('withAttribute')->with('user', '37')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('37');

        $this->assertEquals(
            '[GET] users/37@Admin',
            $dispatcher->dispatch($request)
        );
    }

    public function testGroup()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRouter(function (Router $router) {
            $router->createRoute(['GET'], '/', HomeController::class, 'index');
            $router->group([
                'prefix' => '/admin',
                'middleware' => [AuthSuccessMiddleware::class],
            ], function (Router $router) {
                $router->createRoute(['GET'], '/', AdminController::class, 'index');
                $router->createRoute(['POST'], '/', AdminController::class, 'action');
                $router->createRoute(['GET'], '/users/{user}', AdminController::class, 'users');
            });
        });

        $this->assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))
        );

        $this->assertEquals(
            '[GET] auth success; [GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))
        );

        $this->assertEquals(
            '[POST] auth success; [POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))
        );

        $request = $this->createRequest('GET', '/admin/users/81');
        $request->shouldReceive('withAttribute')->with('user', '81')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('81');

        $this->assertEquals(
            '[GET] auth success; [GET] users/81@Admin',
            $dispatcher->dispatch($request)
        );
    }

    public function testPrefix()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRouter(function (Router $router) {
            $router->createRoute(['GET'], '/', HomeController::class, 'index');
            $router->prefix('/admin', function (Router $router) {
                $router->createRoute(['GET'], '/', AdminController::class, 'index');
                $router->createRoute(['POST'], '/', AdminController::class, 'action');
                $router->createRoute(['GET'], '/users/{user}', AdminController::class, 'users');
            });
        });

        $this->assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))
        );

        $this->assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))
        );

        $this->assertEquals(
            '[POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))
        );

        $request = $this->createRequest('GET', '/admin/users/81');
        $request->shouldReceive('withAttribute')->with('user', '81')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('81');

        $this->assertEquals(
            '[GET] users/81@Admin',
            $dispatcher->dispatch($request)
        );
    }

    public function testMiddlewares()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRouter(function (Router $router) {
            $router->middlewares([AuthSuccessMiddleware::class], function (Router $router) {
                $router->createRoute(['GET'], '/admin', AdminController::class, 'index');
                $router->createRoute(['POST'], '/admin', AdminController::class, 'action');
                $router->createRoute(['GET'], '/admin/users/{user}', AdminController::class, 'users');
            });
        });

        $this->assertEquals(
            '[GET] auth success; [GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))
        );

        $this->assertEquals(
            '[POST] auth success; [POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))
        );

        $request = $this->createRequest('GET', '/admin/users/83');
        $request->shouldReceive('withAttribute')->with('user', '83')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('83');

        $this->assertEquals(
            '[GET] auth success; [GET] users/83@Admin',
            $dispatcher->dispatch($request)
        );
    }

    public function testVirtualMethod()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRouter(function (Router $router) {
            $router->createRoute(['PUT'], '/', HomeController::class);
        });

        $request = $this->createRequest('POST', '/');
        $request->shouldReceive('getParsedBody')->andReturn([
            '_method' => 'put'
        ]);
        $request->shouldReceive('withMethod')->with('PUT')->andReturn(
            $this->createRequest('PUT', '/') // changed!
        );

        try {
            $dispatcher->dispatch($request);
            $this->fail();
        } catch (MethodNotAllowedException $e) {
        }
    }

    public function testVirtualMethodEnabled()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader(), [
            'virtual_method_enabled' => true,
        ]))->withRouter(function (Router $router) {
            $router->createRoute(['PUT'], '/', HomeController::class);
        });

        $request = $this->createRequest('POST', '/');
        $request->shouldReceive('getParsedBody')->andReturn([
            '_method' => 'put'
        ]);
        $request->shouldReceive('withMethod')->with('PUT')->andReturn(
            $this->createRequest('PUT', '/') // changed!
        );

        $this->assertEquals(
            '[PUT] index@Home',
            $dispatcher->dispatch($request)
        );
    }
}
