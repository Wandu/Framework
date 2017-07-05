<?php
namespace Wandu\Router;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Assertions;
use Wandu\Http\Psr\Stream\StringStream;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Loader\DefaultLoader;

class DispatcherTest extends TestCase
{
    use Assertions;
    
    public function testSimpleDispatcher()
    {
        $dispatcher = $this->createDispatcher();
        
        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/', TestDispatcherHomeController::class);
        });

        static::assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))->getBody()->__toString()
        );
    }

    public function testDispatchMethod()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/admin', TestDispatcherAdminController::class, 'index');
            $router->createRoute(['POST'], '/admin', TestDispatcherAdminController::class, 'action');
        });

        static::assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))->getBody()->__toString()
        );
        static::assertEquals(
            '[POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))->getBody()->__toString()
        );

        foreach (['PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $dispatcher->dispatch($this->createRequest($method, '/admin'));
                static::fail();
            } catch (MethodNotAllowedException $exception) {
            }
        }
    }

    public function testDispatchMatchingUri()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/admin/index', TestDispatcherAdminController::class, 'index');
            $router->createRoute(['GET'], '/admin/action', TestDispatcherAdminController::class, 'action');
        });

        static::assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin/index'))->getBody()->__toString()
        );
        static::assertEquals(
            '[GET] action@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin/action'))->getBody()->__toString()
        );
    }

    public function testDispatchMatchingRegExpUri()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/admin/users/:user', TestDispatcherAdminController::class, 'users');
        });

        $request = $this->createRequest('GET', '/admin/users/37');
        $request->shouldReceive('withAttribute')->with('user', '37')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('37');

        static::assertEquals(
            '[GET] users/37@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testGroup()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/', TestDispatcherHomeController::class, 'index');
            $router->group([
                'prefix' => '/admin',
                'middleware' => [TestDispatcherMiddleware::class],
            ], function (Router $router) {
                $router->createRoute(['GET'], '/', TestDispatcherAdminController::class, 'index');
                $router->createRoute(['POST'], '/', TestDispatcherAdminController::class, 'action');
                $router->createRoute(['GET'], '/users/:user', TestDispatcherAdminController::class, 'users');
            });
        });

        static::assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))->getBody()->__toString()
        );

        static::assertEquals(
            '[GET] auth success; [GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))->getBody()->__toString()
        );

        static::assertEquals(
            '[POST] auth success; [POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/admin/users/81');
        $request->shouldReceive('withAttribute')->with('user', '81')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('81');

        static::assertEquals(
            '[GET] auth success; [GET] users/81@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testPrefix()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/', TestDispatcherHomeController::class, 'index');
            $router->prefix('/admin', function (Router $router) {
                $router->createRoute(['GET'], '/', TestDispatcherAdminController::class, 'index');
                $router->createRoute(['POST'], '/', TestDispatcherAdminController::class, 'action');
                $router->createRoute(['GET'], '/users/:user', TestDispatcherAdminController::class, 'users');
            });
        });

        static::assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', ''))->getBody()->__toString()
        );

        static::assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($this->createRequest('GET', '/'))->getBody()->__toString()
        );

        static::assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))->getBody()->__toString()
        );

        static::assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin/'))->getBody()->__toString()
        );

        static::assertEquals(
            '[POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/admin/users/81');
        $request->shouldReceive('withAttribute')->with('user', '81')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('81');

        static::assertEquals(
            '[GET] users/81@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testDomain()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['GET'], '/', TestDispatcherHomeController::class, 'index');
            $router->createRoute(['GET'], '/something', TestDispatcherHomeController::class, 'something');
            $router->domain(["admin.wandu.io", "admin.wandu.dev"], function (Router $router) {
                $router->createRoute(['GET'], '/', TestDispatcherAdminController::class, 'index');
                $router->createRoute(['POST'], '/', TestDispatcherAdminController::class, 'action');
                $router->createRoute(['GET'], '/users/:user', TestDispatcherAdminController::class, 'users');
            });
        });

        $request = $this->createRequest('GET', '/');
        static::assertEquals(
            '[GET] index@Home',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/', 'admin.wandu.io');
        static::assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );

        // cannot access
        static::assertException(new RouteNotFoundException(), function () use ($dispatcher) {
            $request = $this->createRequest('GET', '/users/30');
            $dispatcher->dispatch($request)->getBody()->__toString();
        });
        static::assertException(new MethodNotAllowedException(), function () use ($dispatcher) {
            $request = $this->createRequest('POST', '/');
            $dispatcher->dispatch($request)->getBody()->__toString();
        });
        static::assertException(new RouteNotFoundException(), function () use ($dispatcher) {
            $request = $this->createRequest('GET', '/users/81');
            $dispatcher->dispatch($request)->getBody()->__toString();
        });
        
        // can access if admin.wandu.io
        $request = $this->createRequest('GET', '/', 'admin.wandu.io');
        static::assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );

        $request = $this->createRequest('POST', '/', 'admin.wandu.io');
        static::assertEquals(
            '[POST] action@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/users/81', 'admin.wandu.io');
        $request->shouldReceive('withAttribute')->with('user', '81')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('81');

        static::assertEquals(
            '[GET] users/81@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );

        // can access if admin.wandu.dev
        $request = $this->createRequest('GET', '/', 'admin.wandu.dev');
        static::assertEquals(
            '[GET] index@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );

        $request = $this->createRequest('POST', '/', 'admin.wandu.dev');
        static::assertEquals(
            '[POST] action@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/users/81', 'admin.wandu.dev');
        $request->shouldReceive('withAttribute')->with('user', '81')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('81');

        static::assertEquals(
            '[GET] users/81@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testMiddlewares()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher->setRoutes(function (Router $router) {
            $router->middleware([TestDispatcherMiddleware::class], function (Router $router) {
                $router->createRoute(['GET'], '/admin', TestDispatcherAdminController::class, 'index');
                $router->createRoute(['POST'], '/admin', TestDispatcherAdminController::class, 'action');
                $router->createRoute(['GET'], '/admin/users/:user', TestDispatcherAdminController::class, 'users');
            });
        });

        static::assertEquals(
            '[GET] auth success; [GET] index@Admin',
            $dispatcher->dispatch($this->createRequest('GET', '/admin'))->getBody()->__toString()
        );

        static::assertEquals(
            '[POST] auth success; [POST] action@Admin',
            $dispatcher->dispatch($this->createRequest('POST', '/admin'))->getBody()->__toString()
        );

        $request = $this->createRequest('GET', '/admin/users/83');
        $request->shouldReceive('withAttribute')->with('user', '83')->andReturn($request);
        $request->shouldReceive('getAttribute')->with('user')->andReturn('83');

        static::assertEquals(
            '[GET] auth success; [GET] users/83@Admin',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testVirtualMethodDisabled()
    {
        $dispatcher = new Dispatcher(new DefaultLoader());
        $dispatcher->setRoutes(function (Router $router) {
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
            static::fail();
        } catch (MethodNotAllowedException $e) {
            static::addToAssertionCount(1); // do nothing
        }
    }

    public function testVirtualMethodByUnderbarMethod()
    {
        $dispatcher = $this->createDispatcher([
            'virtual_method_enabled' => true,
        ]);
        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['PUT'], '/', TestDispatcherHomeController::class);
        });

        $request = $this->createRequest('POST', '/');
        $request->shouldReceive('getParsedBody')->andReturn([
            '_method' => 'put'
        ]);
        $request->shouldReceive('withMethod')->with('PUT')->andReturn(
            $this->createRequest('PUT', '/') // changed!
        );

        static::assertEquals(
            '[PUT] index@Home',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }

    public function testVirtualMethodByXHeader()
    {
        $dispatcher = $this->createDispatcher([
            'virtual_method_enabled' => true,
        ]);
        $dispatcher->setRoutes(function (Router $router) {
            $router->createRoute(['PUT'], '/', TestDispatcherHomeController::class);
        });

        $request = $this->createRequest('POST', '/');
        $request->shouldReceive('getParsedBody')->andReturn([]);
        $request->shouldReceive('hasHeader')->with('X-Http-Method-Override')->andReturn(true);
        $request->shouldReceive('getHeaderLine')->with('X-Http-Method-Override')->andReturn('PUT');
        $request->shouldReceive('withMethod')->with('PUT')->andReturn(
            $this->createRequest('PUT', '/') // changed!
        );

        static::assertEquals(
            '[PUT] index@Home',
            $dispatcher->dispatch($request)->getBody()->__toString()
        );
    }
}

class TestDispatcherHomeController
{
    static public function index(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] index@Home";
    }

    static public function something(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] something@Home";
    }
}

class TestDispatcherAdminController
{
    static public function index(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] index@Admin";
    }

    static public function action(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] action@Admin";
    }

    static public function users(ServerRequestInterface $request)
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
