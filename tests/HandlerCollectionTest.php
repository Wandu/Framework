<?php
namespace Jicjjang\June;

use ArrayAccess;
use Jicjjang\June\Stubs\AdminController;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Mockery;
use ArrayObject;

class HandlerCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testExecuteWithHandler()
    {
        $executeMock = Mockery::mock(ServerRequestInterface::class);

        $handlers = new HandlerCollection(new ArrayObject(), [
            function (ServerRequestInterface $req) {
                return 'hi';
            }
        ]);

        $this->assertEquals('hi', $handlers->execute($executeMock));
        $this->assertEquals('hi', $handlers->execute($executeMock));
    }

    public function testExecuteWithMiddlewareAndNext()
    {
        $executeMock = Mockery::mock(ServerRequestInterface::class);

        $handlers = new HandlerCollection(new ArrayObject(), [
                function (ServerRequestInterface $req, \Closure $next) {
                    return $next($req) . ' world';
                },
                function (ServerRequestInterface $req) {
                    return 'hello';
                }
            ]);

        $this->assertEquals('hello world', $handlers->execute($executeMock));
        $this->assertEquals('hello world', $handlers->execute($executeMock));
    }

    public function testExecuteWithMiddleware()
    {
        $executeMock = Mockery::mock(ServerRequestInterface::class);

        $handlers = new HandlerCollection(new ArrayObject(), [
                function (ServerRequestInterface $req, \Closure $next) {
                    return 'world';
                },
                function (ServerRequestInterface $req) {
                    return 'hello';
                }
            ]);

        $this->assertEquals('world', $handlers->execute($executeMock));
        $this->assertEquals('world', $handlers->execute($executeMock));
    }

    public function testExecuteWithMiddlewares()
    {
        $executeMock = Mockery::mock(ServerRequestInterface::class);

        $handlers = new HandlerCollection(new ArrayObject(), [
            function (ServerRequestInterface $req, \Closure $next) {
                return $next($req) . ' the world';
            },
            function (ServerRequestInterface $req, \Closure $next) {
                return $next($req) . ' and';
            },
            function (ServerRequestInterface $req, \Closure $next) {
                return $next($req) . ' world';
            },
            function (ServerRequestInterface $req) {
                return 'hello';
            },
        ]);

        $this->assertEquals('hello world and the world', $handlers->execute($executeMock));
        $this->assertEquals('hello world and the world', $handlers->execute($executeMock));
    }

    public function testControllers()
    {
        $mockServerRequest = Mockery::mock(ServerRequestInterface::class);

        $mockControllers = Mockery::mock(ArrayAccess::class);
        $mockControllers->shouldReceive('offsetGet')->with('Admin')->andReturn(new AdminController());

        $handlers = new HandlerCollection($mockControllers, [
            'auth@Admin',
            'index@Admin'
        ]);

        $this->assertEquals('hello world...', $handlers->execute($mockServerRequest));
    }
}
