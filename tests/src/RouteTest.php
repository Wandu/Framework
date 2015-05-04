<?php
namespace June;

use PHPUnit_Framework_TestCase;

class RouteTest extends PHPUnit_Framework_TestCase
{
    protected $request;

    public function setUp()
    {
        $this->route[0] = new Route('GET', '/', function () {
            print_r('hi');
        });
        $this->route[1] = new Route('GET', '/test/', function () {
            print_r('hi');
        });
        $this->route[2] = new Route('GET', '/test?value=1&asdf=3', function () {
            print_r('hi');
        });
        $this->route[3] = new Route('GET', '/test;param;param?value=1&asdf=4', function () {
            print_r('hi');
        });
        $this->route[4] = new Route('POST', '/test/{id}', function () {
            print_r('hi');
        });
        $this->route[5] = new Route('POST', '/test/{page}/{num}', function () {
            print_r('hi');
        });
    }

    public function testIsExecutable()
    {

    }

    public function testExecute()
    {

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

    public function testMiddleWare()
    {
        //$route = new Route;
//        $route->get(
//            function (RequestInterface, Closure) {},
//            function (RequestInterface, Closure) {},
//            function (RequestInterface) {
//                return ResponseInterface
//            }
//        );
    }
}
