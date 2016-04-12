<?php
namespace Wandu\Router\Issues;

use Mockery;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Route;
use Wandu\Router\Stubs\AuthController;
use Wandu\Router\Stubs\CookieMiddleware;
use Wandu\Router\TestCase;

class Issue1Test extends TestCase
{
    public function testDispatch()
    {
        $changedRequest = $this->createRequest('GET', '/');
        $changedRequest->shouldReceive('getAttribute')->once()
            ->with('cookie', [])->andReturn(['name' => 'wan2land']);

        $request = $this->createRequest('GET', '/');
        $request->shouldReceive('withAttribute')->once()
            ->with('cookie', ['name' => 'wan2land'])->andReturn($changedRequest);

        $route = new Route(AuthController::class, 'login', [
            CookieMiddleware::class
        ]);

        $this->assertEquals(
            'login@Auth, cookie={"name":"wan2land"}',
            $route->execute($request, new DefaultLoader())
        );
    }
}
