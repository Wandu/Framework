<?php
namespace June;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public $app;

    public function setUp()
    {
        $this->app = new Router();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testMethods()
    {
        $this->assertEquals(0, $this->app->count());

        $handler = function () {
            return "!!!";
        };
        $this->app->get('/', $handler);
        $this->assertEquals(1, $this->app->count());

        $this->app->post('/', $handler);
        $this->assertEquals(2, $this->app->count());

        $this->app->put('/', $handler);
        $this->assertEquals(3, $this->app->count());

        $this->app->delete('/', $handler);
        $this->assertEquals(4, $this->app->count());

        $this->app->options('/', $handler);
        $this->assertEquals(5, $this->app->count());

//        $this->assertAttributeContains(['/', $handler], 'methodGetRoutes', $this->app);
    }

    public function testDispatch()
    {
        $getMock = Mockery::mock(RequestInterface::class);
        $getMock->shouldReceive('getMethod')->andReturn('GET');
        $getMock->shouldReceive('getUri')->andReturn('/');

        $postMock = Mockery::mock(RequestInterface::class);
        $postMock->shouldReceive('getMethod')->andReturn('POST');
        $postMock->shouldReceive('getUri')->andReturn('/');

        $getCalled = 0;
        $postCalled = 0;
        $this->app->get('/', function () use (&$getCalled) {
            $getCalled++;
            return 'get';
        });

        $this->app->post('/', function () use (&$postCalled) {
            $postCalled++;
            return 'post';
        });

        $this->assertEquals('get', $this->app->dispatch($getMock));
        $this->assertEquals(1, $getCalled);
        $this->assertEquals(0, $postCalled);

        $this->assertEquals('post', $this->app->dispatch($postMock));
        $this->assertEquals(1, $getCalled);
        $this->assertEquals(1, $postCalled);
    }
}
