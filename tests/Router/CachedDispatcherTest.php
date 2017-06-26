<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Psr\ServerRequest;

class CachedDispatcherTest extends TestCase
{
    public function setUp()
    {
        @unlink(__DIR__ . '/router.cache.php');
    }

    public function testDispatch()
    {
        $dispatcher = $this->createDispatcher();

        $count = 0;
        $routes = function (Router $router) use (&$count) {
            $count++;
            $router->get('admin', TestCachedDispatcherController::class, 'index');
        };
        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        static::assertEquals(4, $count);
    }

    public function testInstanceCache()
    {
        $dispatcher = $this->createDispatcher();

        $count = 0;
        $routes = function (Router $router) use (&$count) {
            $count++;
            $router->get('admin', TestCachedDispatcherController::class, 'index');
        };
        $dispatcher->setRoutes($routes);

        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        static::assertEquals(1, $count);
    }

    public function testDispatchWithCache()
    {
        $dispatcher = $this->createDispatcher([
            'cache_enabled' => true,
            'cache_file' => __DIR__ . '/router.cache.php',
        ]);

        $count = 0;
        $routes = function (Router $router) use (&$count) {
            $count++;
            $router->get('admin', TestCachedDispatcherController::class, 'index');
        };

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        static::assertEquals(1, $count);
    }

    public function testFlush()
    {
        $dispatcher = $this->createDispatcher([
            'cache_enabled' => true,
            'cache_file' => __DIR__ . '/router.cache.php',
        ]);

        $count = 0;
        $routes = function (Router $router) use (&$count) {
            $count++;
            $router->get('admin', TestCachedDispatcherController::class, 'index');
        };
        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));
        
        $dispatcher->flush();

        $dispatcher->setRoutes($routes);
        $dispatcher->dispatch(new ServerRequest([], [], [], [], [], [], 'GET', '/admin'));

        static::assertEquals(2, $count);
    }
}

class TestCachedDispatcherController
{
    public static function index(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] index@Admin";
    }

    public static function action(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] action@Admin";
    }

    public static function users(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] users/{$request->getAttribute('user')}@Admin";
    }
}
