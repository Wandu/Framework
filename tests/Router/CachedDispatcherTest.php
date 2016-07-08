<?php
namespace Wandu\Router;

use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\RoutesInterface;

class CachedDispatcherTest extends TestCase
{
    public function setUp()
    {
        @unlink(__DIR__ . '/router.cache.php');
    }

    public function testDispatchWithNonCache()
    {
        $dispatcher = $this->createDispatcher();

        $dispatcher = $dispatcher->withRoutes(
            $routes = new class implements RoutesInterface
            {
                public $loadCount = 0;

                public function routes(Router $router)
                {
                    $this->loadCount++;
                    $router->get('admin', TestCachedDispatcherController::class, 'index');
                }
            }
        );

        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));

        $this->assertEquals(3, $routes->loadCount);
    }

    public function testDispatchWithCache()
    {
        $dispatcher = $this->createDispatcher([
            'cache_enabled' => true,
            'cache_file' => __DIR__ . '/router.cache.php',
        ]);

        $dispatcher = $dispatcher->withRoutes(
            $routes = new class implements RoutesInterface
            {
                public $loadCount = 0;

                public function routes(Router $router)
                {
                    $this->loadCount++;
                    $router->get('admin', TestCachedDispatcherController::class, 'index');
                }
            }
        );

        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));

        $this->assertEquals(1, $routes->loadCount);
    }

    public function testFlush()
    {
        $dispatcher = $this->createDispatcher([
            'cache_enabled' => true,
            'cache_file' => __DIR__ . '/router.cache.php',
        ]);
        
        $dispatcher = $dispatcher->withRoutes(
            $routes = new class implements RoutesInterface
            {
                public $loadCount = 0;

                public function routes(Router $router)
                {
                    $this->loadCount++;
                    $router->get('admin', TestCachedDispatcherController::class, 'index');
                }
            }
        );

        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->flush();
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));

        $this->assertEquals(2, $routes->loadCount);
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
