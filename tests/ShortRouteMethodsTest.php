<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Stubs\HomeController;

class ShortRouteMethods extends PHPUnit_Framework_TestCase
{
    /** @var Router */
    protected $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGet()
    {
        $this->router->get('/', HomeController::class, 'index');

        // success
        foreach (['GET', 'HEAD'] as $method) {
            $this->assertEquals('index@Home', $this->router->dispatch($this->createRequest($method, '/')));
        }
        // fail
        foreach (['POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $this->router->dispatch($this->createRequest($method, '/'));
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testPost()
    {
        $this->router->post('/', HomeController::class, 'index');

        // success
        foreach (['POST'] as $method) {
            $this->assertEquals('index@Home', $this->router->dispatch($this->createRequest($method, '/')));
        }
        // fail
        foreach (['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $this->router->dispatch($this->createRequest($method, '/'));
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testPut()
    {
        $this->router->put('/', HomeController::class, 'index');

        // success
        foreach (['PUT'] as $method) {
            $this->assertEquals('index@Home', $this->router->dispatch($this->createRequest($method, '/')));
        }
        // fail
        foreach (['GET', 'HEAD', 'POST', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $this->router->dispatch($this->createRequest($method, '/'));
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testDelete()
    {
        $this->router->delete('/', HomeController::class, 'index');

        // success
        foreach (['DELETE'] as $method) {
            $this->assertEquals('index@Home', $this->router->dispatch($this->createRequest($method, '/')));
        }
        // fail
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'OPTIONS', 'PATCH'] as $method) {
            try {
                $this->router->dispatch($this->createRequest($method, '/'));
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testOptions()
    {
        $this->router->options('/', HomeController::class, 'index');

        // success
        foreach (['OPTIONS'] as $method) {
            $this->assertEquals('index@Home', $this->router->dispatch($this->createRequest($method, '/')));
        }
        // fail
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
            try {
                $this->router->dispatch($this->createRequest($method, '/'));
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testPatch()
    {
        $this->router->patch('/', HomeController::class, 'index');

        // success
        foreach (['PATCH'] as $method) {
            $this->assertEquals('index@Home', $this->router->dispatch($this->createRequest($method, '/')));
        }
        // fail
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS'] as $method) {
            try {
                $this->router->dispatch($this->createRequest($method, '/'));
                $this->fail();
            } catch (MethodNotAllowedException $e) {
            }
        }
    }

    public function testAny()
    {
        $this->router->any('/', HomeController::class, 'index');

        // success
        foreach (['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'] as $method) {
            $this->assertEquals('index@Home', $this->router->dispatch($this->createRequest($method, '/')));
        }
    }

    protected function createRequest($method, $path)
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);
        $request->shouldReceive('getMethod')->once()->andReturn($method);
        $request->shouldReceive('getUri->getPath')->once()->andReturn($path);
        return $request;
    }
}
