<?php
namespace June;

use PHPUnit_Framework_TestCase;

class RouteTest extends PHPUnit_Framework_TestCase
{
    protected $request;

    public function setUp()
    {
        $this->request[0] = new Route([
            'method'=> 'GET',
            'path'=>'/'
        ]);
        $this->request[1] = new Route([
            'method'=> 'GET',
            'path'=> '/test/'
        ]);
        $this->request[2] = new Route([
            'method'=> 'GET',
            'path'=> '/test?value=1&asdf=3'
        ]);
        $this->request[3] = new Route([
            'method'=> 'GET',
            'path'=> '/test;param;param?value=1&asdf=4'
        ]);
        $this->request[4] = new Route([
            'method' => 'POST',
            'path' => '/test/{id}'
        ]);
        $this->request[5] = new Route([
            'method' => 'POST',
            'path' => '/test/{page}/{num}'
        ]);
    }

    public function testGetUri()
    {
        $this->assertEquals('', $this->request[0]->getUri());
        $this->assertEquals('test', $this->request[1]->getUri());
        $this->assertEquals('test', $this->request[2]->getUri());
        $this->assertEquals('test', $this->request[3]->getUri());
    }

    public function testGetArgs()
    {
        $this->assertEquals(array(), $this->request[0]->getArgs());
        $this->assertEquals(array(), $this->request[1]->getArgs());
        $this->assertEquals(array(), $this->request[2]->getArgs());
        $this->assertEquals(array(), $this->request[3]->getArgs());
        $this->assertEquals(array('id'), $this->request[4]->getArgs());
        $this->assertEquals(array('page', 'num'), $this->request[5]->getArgs());
    }

    public function testGetPattern()
    {
        $this->assertEquals('', $this->request[0]->getPattern());
        $this->assertEquals('test', $this->request[1]->getPattern());
        $this->assertEquals('test', $this->request[2]->getPattern());
        $this->assertEquals('test', $this->request[3]->getPattern());
        $this->assertEquals("test\/(?<id>[^\/\#]+)", $this->request[4]->getPattern());
        $this->assertEquals("test\/(?<page>[^\/\#]+)\/(?<num>[^\/\#]+)", $this->request[5]->getPattern());
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
