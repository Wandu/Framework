<?php
namespace June;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
//use Mockery\MockInterface;
use Mockery;
use SebastianBergmann\Comparator\ObjectComparatorTest;

class RouteTest extends PHPUnit_Framework_TestCase
{


    protected $route;

    public function setUp()
    {
        $this->route[0] = new Route('GET', '/', function (RequestInterface $req) {
            return 'hi';
        });
        $this->route[1] = new Route('GET', '/test/', function (RequestInterface $req) {
            return 'hi';
        });
        $this->route[2] = new Route('GET', '/test?value=1&asdf=3', function (RequestInterface $req) {
            return 'hi';
        });
        $this->route[3] = new Route('GET', '/test;param;param?value=1&asdf=4', function (RequestInterface $req) {
            return 'hi';
        });
        $this->route[4] = new Route('POST', '/test/{id}', function (RequestInterface $req) {
            return 'hi';
        });
        $this->route[5] = new Route('POST', '/test/{page}/{num}', function (RequestInterface $req) {
            return 'hi';
        });
    }

    public function testExecute()
    {
        $executeMock = Mockery::mock(RequestInterface::class);

        $this->assertEquals('hi', $this->route[0]->execute($executeMock));
        $this->assertEquals('hi', $this->route[1]->execute($executeMock));
        $this->assertEquals('hi', $this->route[2]->execute($executeMock));
        $this->assertEquals('hi', $this->route[3]->execute($executeMock));
        $this->assertEquals('hi', $this->route[4]->execute($executeMock));
        $this->assertEquals('hi', $this->route[5]->execute($executeMock));

        $route = new Route(
            'GET',
            'path',
            function (RequestInterface $req) {
                return 'hello';
            },
            [
                function (RequestInterface $req, \Closure $next) {
                    return $next($req) . ' world';
                }
            ]
        );

        $this->assertEquals('hello world', $route->execute($executeMock));

        $route = new Route(
            'GET',
            'path',
            function (RequestInterface $req) {
                return 'hello';
            },
            [
                function (RequestInterface $req, \Closure $next) {
                    return 'world';
                }
            ]
        );

        $this->assertEquals('world', $route->execute($executeMock));

        $route = new Route(
            'GET',
            'path',
            function (RequestInterface $req) {
                return 'hello';
            },
            [
                function (RequestInterface $req, \Closure $next) {
                    return $next($req) . ' the world';
                },
                function (RequestInterface $req, \Closure $next) {
                    return $next($req) . ' and';
                },
                function (RequestInterface $req, \Closure $next) {
                    return $next($req) . ' world';
                }
            ]
        );

        $this->assertEquals('hello world and the world', $route->execute($executeMock));

    }

    public function testGetSetMiddleware()
    {
        $this->route[0]->setMiddleware([
            function () {
                return 'first';
            }, function () {
                return 'second';
            }, function () {
            }
            ]);
        $this->assertCount(3, $this->route[0]->getMiddleware());
    }

    public function testGetArgs()
    {
        $this->assertEquals(array(), $this->route[0]->getArgs());
        $this->assertEquals(array(), $this->route[1]->getArgs());
        $this->assertEquals(array(), $this->route[2]->getArgs());
        $this->assertEquals(array(), $this->route[3]->getArgs());
        $this->assertEquals(array('id'), $this->route[4]->getArgs());
        $this->assertEquals(array('page', 'num'), $this->route[5]->getArgs());
    }

    public function testGetPattern()
    {
        $this->assertEquals('', $this->route[0]->getPattern());
        $this->assertEquals('test', $this->route[1]->getPattern());
        $this->assertEquals('test', $this->route[2]->getPattern());
        $this->assertEquals('test', $this->route[3]->getPattern());
        $this->assertEquals("test\/(?<id>[^\/\#]+)", $this->route[4]->getPattern());
        $this->assertEquals("test\/(?<page>[^\/\#]+)\/(?<num>[^\/\#]+)", $this->route[5]->getPattern());
    }
}
