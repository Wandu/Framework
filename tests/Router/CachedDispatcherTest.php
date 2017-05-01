<?php
namespace Wandu\Router;

use Mockery;
use Psr\Http\Message\ServerRequestInterface;

class CachedDispatcherTest extends TestCase
{
    public function setUp()
    {
        @unlink(__DIR__ . '/router.cache.php');
    }

    public function testDispatchWithNonCache()
    {
        $dispatcher = $this->createDispatcher();

        $count = 0;
        $routes = function (Router $router) use (&$count) {
            $count++;
            $router->get('admin', TestCachedDispatcherController::class, 'index');
        };
        $dispatcher->setRoutes($routes);
        $dispatcher->setRoutes($routes);
        $dispatcher->setRoutes($routes);
        $dispatcher->setRoutes($routes);

        static::assertEquals(4, $count);
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
        $dispatcher->setRoutes($routes);
        $dispatcher->setRoutes($routes);
        $dispatcher->setRoutes($routes);

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
        $dispatcher->setRoutes($routes);
        $dispatcher->setRoutes($routes);
        $dispatcher->setRoutes($routes);
        $dispatcher->setRoutes($routes);

        $dispatcher->flush();
        $dispatcher->setRoutes($routes);

        static::assertEquals(2, $count);
    }
}

class TestCachedDispatcherController
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
