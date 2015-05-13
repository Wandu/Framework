<?php
namespace Jicjjang\June;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
use Mockery;
use ArrayObject;

class HandlerCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testExecuteWithHandler()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $handlers = new HandlerCollection(new ArrayObject(), [
            function (RequestInterface $req) {
                return 'hi';
            }
        ]);

        $this->assertEquals('hi', $handlers->execute($executeMock));
        $this->assertEquals('hi', $handlers->execute($executeMock));
    }

    public function testExecuteWithMiddlewareAndNext()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $handlers = new HandlerCollection(new ArrayObject(), [
                function (RequestInterface $req, \Closure $next) {
                    return $next($req) . ' world';
                },
                function (RequestInterface $req) {
                    return 'hello';
                }
            ]);

        $this->assertEquals('hello world', $handlers->execute($executeMock));
        $this->assertEquals('hello world', $handlers->execute($executeMock));
    }

    public function testExecuteWithMiddleware()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $handlers = new HandlerCollection(new ArrayObject(), [
                function (RequestInterface $req, \Closure $next) {
                    return 'world';
                },
                function (RequestInterface $req) {
                    return 'hello';
                }
            ]);

        $this->assertEquals('world', $handlers->execute($executeMock));
        $this->assertEquals('world', $handlers->execute($executeMock));
    }

    public function testExecuteWithMiddlewares()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $handlers = new HandlerCollection(new ArrayObject(), [
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

        $this->assertEquals('hello world and the world', $handlers->execute($executeMock));
        $this->assertEquals('hello world and the world', $handlers->execute($executeMock));
    }
}
