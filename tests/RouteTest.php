<?php
namespace Wandu\Router;

use ArrayAccess;
use Wandu\Router\Stubs\AdminController;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Mockery;
use ArrayObject;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testExecuteWithHandler()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getAttribute')->andReturn('hello string~');

        $handlers = new Route(function (ServerRequestInterface $request) {
            return $request->getAttribute('hello');
        });

        $this->assertEquals('hello string~', $handlers->execute($mockRequest));
        $this->assertEquals('hello string~', $handlers->execute($mockRequest));
    }

    public function testExecuteWithStringHandler()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getAttribute')->andReturn('hello string~');

        $mockAccessor = Mockery::mock(MapperInterface::class);
        $mockAccessor
            ->shouldReceive('mapHandler')
            ->with('hello@Admin')
            ->andReturn(function (ServerRequestInterface $request) {
                return $request->getAttribute('hello');
            });

        $handlers = new Route('hello@Admin');

        $this->assertEquals('hello string~', $handlers->execute($mockRequest, $mockAccessor));
        $this->assertEquals('hello string~', $handlers->execute($mockRequest, $mockAccessor));
    }

    public function testHandleWithMiddleware()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);

        $handlers = new Route(function () {
            return 'hello';
        }, [
            function (ServerRequestInterface $req, \Closure $next) {
                return $next($req) . ' world';
            }
        ]);

        $this->assertEquals('hello world', $handlers->execute($mockRequest));
        $this->assertEquals('hello world', $handlers->execute($mockRequest));
    }

    public function testPreventHandleWithMiddleware()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);

        $handlers = new Route(function () {
            return 'hello';
        }, [
            function () {
                return 'prevent by middleware';
            }
        ]);

        $this->assertEquals('prevent by middleware', $handlers->execute($mockRequest));
        $this->assertEquals('prevent by middleware', $handlers->execute($mockRequest));
    }

    public function testExecuteWithManyMiddlewares()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);

        $handlers = new Route(function () {
            return 'hello';
        }, [
            function (ServerRequestInterface $req, \Closure $next) {
                return $next($req) . ' the world';
            },
            function (ServerRequestInterface $req, \Closure $next) {
                return $next($req) . ' and';
            },
            function (ServerRequestInterface $req, \Closure $next) {
                return $next($req) . ' world';
            },
        ]);

        $this->assertEquals('hello world and the world', $handlers->execute($mockRequest));
        $this->assertEquals('hello world and the world', $handlers->execute($mockRequest));
    }

    public function testExecuteWithStringMiddleware()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getAttribute')->andReturn('hello string~');

        $mockAccessor = Mockery::mock(MapperInterface::class);
        $mockAccessor
            ->shouldReceive('mapMiddleware')
            ->with('Middleware')
            ->andReturn(function (ServerRequestInterface $request) {
                return $request->getAttribute('hello');
            });

        $handlers = new Route('hello@Admin', ['Middleware']);

        $this->assertEquals('hello string~', $handlers->execute($mockRequest, $mockAccessor));
        $this->assertEquals('hello string~', $handlers->execute($mockRequest, $mockAccessor));
    }
}
