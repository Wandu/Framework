<?php
namespace Wandu\Router;

use Mockery;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Stubs\AdminController;

class CachedDispatcherTest extends TestCase
{
    public function setUp()
    {
        @unlink(__DIR__ . '/router.cache.php');
    }

    public function testDispatchWithNonCache()
    {
        $dispatcher = new Dispatcher(new DefaultLoader());

        $loadCount = 0;
        $dispatcher = $dispatcher->withRouter(function (Router $router) use (&$loadCount) {
            $loadCount++;
            $router->get('admin', AdminController::class, 'index');
        });

        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));

        $this->assertEquals(3, $loadCount);
    }

    public function testDispatchWithCache()
    {
        $dispatcher = new Dispatcher(new DefaultLoader(), [
            'cache_enabled' => true,
            'cache_file' => __DIR__ . '/router.cache.php',
        ]);

        $loadCount = 0;
        $dispatcher = $dispatcher->withRouter(function (Router $router) use (&$loadCount) {
            $loadCount++;
            $router->get('admin', AdminController::class, 'index');
        });

        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));

        $this->assertEquals(1, $loadCount);
    }

    public function testFlush()
    {
        $dispatcher = new Dispatcher(new DefaultLoader(), [
            'cache_enabled' => true,
            'cache_file' => __DIR__ . '/router.cache.php',
        ]);

        $loadCount = 0;
        $dispatcher = $dispatcher->withRouter(function (Router $router) use (&$loadCount) {
            $loadCount++;
            $router->get('admin', AdminController::class, 'index');
        });

        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));
        $dispatcher->flush();
        $dispatcher->dispatch($this->createRequest('GET', '/admin'));

        $this->assertEquals(2, $loadCount);
    }
}
