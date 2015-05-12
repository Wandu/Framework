<?php
namespace June;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
use Mockery;

class HandlerCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testExecuteWithHandler()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $route = new HandlerCollection([
            function (RequestInterface $req) {
                return 'hi';
            }
        ]);

        $this->assertEquals('hi', $route->execute($executeMock));
        $this->assertEquals('hi', $route->execute($executeMock));
    }

    public function testExceuteWithMiddlewareAndNext()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $route = new HandlerCollection([
                function (RequestInterface $req, \Closure $next) {
                    return $next($req) . ' world';
                },
                function (RequestInterface $req) {
                    return 'hello';
                }
            ]);

        $this->assertEquals('hello world', $route->execute($executeMock));
        $this->assertEquals('hello world', $route->execute($executeMock));
    }

    public function testExecuteWithMiddleware()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $route = new HandlerCollection([
                function (RequestInterface $req, \Closure $next) {
                    return 'world';
                },
                function (RequestInterface $req) {
                    return 'hello';
                }
            ]);

        $this->assertEquals('world', $route->execute($executeMock));
        $this->assertEquals('world', $route->execute($executeMock));
    }

    public function testExecuteWithMiddlewares()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $route = new HandlerCollection([
            function (RequestInterface $req, \Closure $next) {
                return $next($req) . ' the world';
            },
            function (RequestInterface $req, \Closure $next) {
                return $next($req) . ' and';
            },
            function (RequestInterface $req, \Closure $next) {
                return $next($req) . ' world';
            },
            function (RequestInterface $req) {
                return 'hello';
            },
        ]);

        $this->assertEquals('hello world and the world', $route->execute($executeMock));
        $this->assertEquals('hello world and the world', $route->execute($executeMock));
    }
}
