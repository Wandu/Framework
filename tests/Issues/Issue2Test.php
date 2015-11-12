<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Stubs\CookieMiddleware;
use Wandu\Router\Stubs\HomeController;

class Issue2Test extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Router\Router */
    protected $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testDispatch()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);
        $request->shouldReceive('getMethod')->once()->andReturn('GET');
        $request->shouldReceive('getUri->getPath')->once()->andReturn('/');


        $this->router->get('/', HomeController::class, 'wrong');
        try {
            $this->router->dispatch($request);
            $this->fail();
        } catch (HandlerNotFoundException $exception) {
            $this->assertEquals(
                '"Wandu\Router\Stubs\HomeController::wrong" is not found.',
                $exception->getMessage()
            );
        }
    }
}
