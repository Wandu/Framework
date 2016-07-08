<?php
namespace Wandu\Router;

use Mockery;
use Wandu\Router\Contracts\RoutesInterface;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Stubs\HomeController;

class ShortRouteMethods extends TestCase
{
    public function testGet()
    {
        $dispatcher = $this->createDispatcher()->withRoutes(
            new class implements RoutesInterface
            {
                public function routes(Router $router) {
                    $router->get('/', HomeController::class, 'index');
                }
            }
        );

        // success
        foreach (['GET', 'HEAD'] as $method) {
            $this->assertEquals("[{$method}] index@Home", $dispatcher->dispatch(
                $this->createRequest($method, '/')
            )->getBody()->__toString());
        }
        // fail
        foreach (['POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $dispatcher->dispatch(
                    $this->createRequest($method, '/')
                );
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testPost()
    {
        $dispatcher = $this->createDispatcher()->withRoutes(
            new class implements RoutesInterface
            {
                public function routes(Router $router)
                {
                    $router->post('/', HomeController::class, 'index');
                }
            }
        );

        // success
        foreach (['POST'] as $method) {
            $this->assertEquals("[{$method}] index@Home", $dispatcher->dispatch(
                $this->createRequest($method, '/')
            )->getBody()->__toString());
        }
        // fail
        foreach (['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $dispatcher->dispatch(
                    $this->createRequest($method, '/')
                );
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testPut()
    {
        $dispatcher = $this->createDispatcher()->withRoutes(
            new class implements RoutesInterface
            {
                public function routes(Router $router)
                {
                    $router->put('/', HomeController::class, 'index');
                }
            }
        );

        // success
        foreach (['PUT'] as $method) {
            $this->assertEquals("[{$method}] index@Home", $dispatcher->dispatch(
                $this->createRequest($method, '/')
            )->getBody()->__toString());
        }
        // fail
        foreach (['GET', 'HEAD', 'POST', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $dispatcher->dispatch(
                    $this->createRequest($method, '/')
                );
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testDelete()
    {
        $dispatcher = $this->createDispatcher()->withRoutes(
            new class implements RoutesInterface
            {
                public function routes(Router $router)
                {

                    $router->delete('/', HomeController::class, 'index');
                }
            }
        );

        // success
        foreach (['DELETE'] as $method) {
            $this->assertEquals("[{$method}] index@Home", $dispatcher->dispatch(
                $this->createRequest($method, '/')
            )->getBody()->__toString());
        }
        // fail
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $dispatcher->dispatch(
                    $this->createRequest($method, '/')
                );
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testOptions()
    {
        $dispatcher = $this->createDispatcher()->withRoutes(
            new class implements RoutesInterface
            {
                public function routes(Router $router)
                {
                    $router->options('/', HomeController::class, 'index');
                }
            }
        );

        // success
        foreach (['OPTIONS'] as $method) {
            $this->assertEquals("[{$method}] index@Home", $dispatcher->dispatch(
                $this->createRequest($method, '/')
            )->getBody()->__toString());
        }
        // fail
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
            try {
                $dispatcher->dispatch(
                    $this->createRequest($method, '/')
                );
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testPatch()
    {
        $dispatcher = $this->createDispatcher()->withRoutes(
            new class implements RoutesInterface
            {
                public function routes(Router $router) {
                    $router->patch('/', HomeController::class, 'index');
                }
            }
        );

        // success
        foreach (['PATCH'] as $method) {
            $this->assertEquals("[{$method}] index@Home", $dispatcher->dispatch(
                $this->createRequest($method, '/')
            )->getBody()->__toString());
        }
        // fail
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS'] as $method) {
            try {
                $dispatcher->dispatch(
                    $this->createRequest($method, '/')
                );
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testAny()
    {
        $dispatcher = $this->createDispatcher()->withRoutes(
            new class implements RoutesInterface
            {
                public function routes(Router $router)
                {
                    $router->any('/', HomeController::class, 'index');
                }
            }
        );

        // success
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            $this->assertEquals("[{$method}] index@Home", $dispatcher->dispatch(
                $this->createRequest($method, '/')
            )->getBody()->__toString());
        }
    }
}
