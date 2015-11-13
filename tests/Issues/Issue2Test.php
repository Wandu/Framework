<?php
namespace Wandu\Router\Issues;

use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\RouterTestCase;
use Wandu\Router\Stubs\HomeController;

class Issue2Test extends RouterTestCase
{
    public function testDispatch()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);
        $request->shouldReceive('getMethod')->once()->andReturn('GET');
        $request->shouldReceive('getUri->getPath')->once()->andReturn('/');


        $this->router->get('/', HomeController::class, 'wrong');
        try {
            $this->dispatcher->dispatch($request, $this->router);
            $this->fail();
        } catch (HandlerNotFoundException $exception) {
            $this->assertEquals(
                '"Wandu\Router\Stubs\HomeController::wrong" is not found.',
                $exception->getMessage()
            );
        }
    }
}
