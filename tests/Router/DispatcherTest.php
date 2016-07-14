<?php
namespace Wandu\Router;

use Closure;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Psr\Stream\StringStream;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\MethodNotAllowedException;

class DispatcherTest extends TestCase
{
    public function testSimpleDispatcher()
    {
        $dispatcher = $this->createDispatcher();
        
        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/', TestDispatcherHomeController::class);
        });

        $this->assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))->getBody()->__toString()
        );
    }

    public function testDispatchMethod()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/admin', TestDispatcherAdminController::class, 'index');
            $router->createRoute(['POST'], '/admin', TestDispatcherAdminController::class, 'action');
        });

        $this->assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))->getBody()->__toString()
        );
        $this->assertEquals(
            '[POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))->getBody()->__toString()
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
        $dispatcher = $this->createDispatcher();

        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/admin/index', TestDispatcherAdminController::class, 'index');
            $router->createRoute(['GET'], '/admin/action', TestDispatcherAdminController::class, 'action');
        });

        $this->assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin/index'))->getBody()->__toString()
        );
        $this->assertEquals(
            '[GET] action@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin/action'))->getBody()->__toString()
        );
    }

    public function testDispatchMatchingRegExpUri()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/admin/users/{user}', TestDispatcherAdminController::class, 'users');
        });

        $request = $this->createRequest('GET', '/admin/users/37');
        $request->shouldReceive('withAttribute')->with('user', '37')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('37');

        $this->assertEquals(
            '[GET] users/37@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testGroup()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/', TestDispatcherHomeController::class, 'index');
            $router->group([
                'prefix' => '/admin',
                'middleware' => [TestDispatcherMiddleware::class],
            ], function (Router $router) {
                $router->createRoute(['GET'], '/', TestDispatcherAdminController::class, 'index');
                $router->createRoute(['POST'], '/', TestDispatcherAdminController::class, 'action');
                $router->createRoute(['GET'], '/users/{user}', TestDispatcherAdminController::class, 'users');
            });
        });

        $this->assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))->getBody()->__toString()
        );

        $this->assertEquals(
            '[GET] auth success; [GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))->getBody()->__toString()
        );

        $this->assertEquals(
            '[POST] auth success; [POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/admin/users/81');
        $request->shouldReceive('withAttribute')->with('user', '81')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('81');

        $this->assertEquals(
            '[GET] auth success; [GET] users/81@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testPrefix()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/', TestDispatcherHomeController::class, 'index');
            $router->prefix('/admin', function (Router $router) {
                $router->createRoute(['GET'], '/', TestDispatcherAdminController::class, 'index');
                $router->createRoute(['POST'], '/', TestDispatcherAdminController::class, 'action');
                $router->createRoute(['GET'], '/users/{user}', TestDispatcherAdminController::class, 'users');
            });
        });

        $this->assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))->getBody()->__toString()
        );

        $this->assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))->getBody()->__toString()
        );

        $this->assertEquals(
            '[POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/admin/users/81');
        $request->shouldReceive('withAttribute')->with('user', '81')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('81');

        $this->assertEquals(
            '[GET] users/81@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testMiddlewares()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $router->middlewares([TestDispatcherMiddleware::class], function (Router $router) {
                $router->createRoute(['GET'], '/admin', TestDispatcherAdminController::class, 'index');
                $router->createRoute(['POST'], '/admin', TestDispatcherAdminController::class, 'action');
                $router->createRoute(['GET'], '/admin/users/{user}', TestDispatcherAdminController::class, 'users');
            });
        });

        $this->assertEquals(
            '[GET] auth success; [GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))->getBody()->__toString()
        );

        $this->assertEquals(
            '[POST] auth success; [POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/admin/users/83');
        $request->shouldReceive('withAttribute')->with('user', '83')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('83');

        $this->assertEquals(
            '[GET] auth success; [GET] users/83@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testVirtualMethod()
    {
        $dispatcher = (new Dispatcher(new DefaultLoader()))->withRoutes(function (Router $router) {
            $router->createRoute(['PUT'], '/', TestDispatcherHomeController::class);
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
        $dispatcher = $this->createDispatcher([
            'virtual_method_enabled' => true,
        ]);
        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $router->createRoute(['PUT'], '/', TestDispatcherHomeController::class);
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
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }
}

class TestDispatcherHomeController
{
    public function index(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] index@Home";
    }
}

class TestDispatcherAdminController
{
    public function index(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] index@Admin";
    }

    public function action(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] action@Admin";
    }

    public function users(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] users/{$request->getAttribute('user')}@Admin";
    }
}

class TestDispatcherMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $next($request);
        $message = "[{$request->getMethod()}] auth success; " . $response->getBody()->__toString();

        return $response->withBody(new StringStream($message));
    }
}
