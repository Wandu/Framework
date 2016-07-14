<?php
namespace Wandu\Router;

use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Exception\MethodNotAllowedException;

class ShortRouteMethods extends TestCase
{
    public function testGet()
    {
        $dispatcher = $this->createDispatcher()->withRoutes(function (Router $router) {
            $router->get('/', TestShortRouteMethodController::class, 'index');
        });

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
        $dispatcher = $this->createDispatcher()->withRoutes(function (Router $router) {
            $router->post('/', TestShortRouteMethodController::class, 'index');
        });

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
        $dispatcher = $this->createDispatcher()->withRoutes(function (Router $router) {
            $router->put('/', TestShortRouteMethodController::class, 'index');
        });

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
        $dispatcher = $this->createDispatcher()->withRoutes(function (Router $router) {
            $router->delete('/', TestShortRouteMethodController::class, 'index');
        });

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
        $dispatcher = $this->createDispatcher()->withRoutes(function (Router $router) {
            $router->options('/', TestShortRouteMethodController::class, 'index');
        });

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
        $dispatcher = $this->createDispatcher()->withRoutes(function (Router $router) {
            $router->patch('/', TestShortRouteMethodController::class, 'index');
        });

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
        $dispatcher = $this->createDispatcher()->withRoutes(function (Router $router) {
            $router->any('/', TestShortRouteMethodController::class, 'index');
        });

        // success
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            $this->assertEquals("[{$method}] index@Home", $dispatcher->dispatch(
                $this->createRequest($method, '/')
            )->getBody()->__toString());
        }
    }
}


class TestShortRouteMethodController
{
    public function index(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] index@Home";
    }
}
