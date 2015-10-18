<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Stubs\AdminController;
use Wandu\Router\Stubs\CookieMiddleware;
use Wandu\Router\Stubs\HomeController;

class Issue1Test extends PHPUnit_Framework_TestCase
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
        $changedRequest = Mockery::mock(ServerRequestInterface::class);
        $changedRequest->shouldReceive('getAttribute')->once()->with('cookie', 'null')->andReturn('cookie~~');

        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);
        $request->shouldReceive('getMethod')->once()->andReturn('GET');
        $request->shouldReceive('getUri->getPath')->once()->andReturn('/');
        $request->shouldReceive('withAttribute')->once()->with('cookie', 'cookie~~')->andReturn($changedRequest);

        $this->router->createRoute(['GET'], '/', HomeController::class, 'login', [
            CookieMiddleware::class
        ]);

        $this->assertEquals('cookie~~', $this->router->dispatch($request));
    }
}
