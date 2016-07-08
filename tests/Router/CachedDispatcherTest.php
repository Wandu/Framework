<?php
namespace Wandu\Router;

use Mockery;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Contracts\RoutesInterface;
use Wandu\Router\Responsifier\WanduResponsifier;
use Wandu\Router\Stubs\AdminController;

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
                    $router->get('admin', AdminController::class, 'index');
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
                    $router->get('admin', AdminController::class, 'index');
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
                    $router->get('admin', AdminController::class, 'index');
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
