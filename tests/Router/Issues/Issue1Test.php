<?php
namespace Wandu\Router\Issues;

use Closure;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Responsifier\WanduResponsifier;
use Wandu\Router\Route;
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

        $route = new Route(TestIssue1Controller::class, 'login', [
            TestIssue1Middleware::class
        ]);

        $response = $route->execute($request, new DefaultLoader(), new WanduResponsifier());
        $this->assertEquals(
            'login@Issue1, cookie={"name":"wan2land"}',
            $response->getBody()->__toString()
        );
    }
}

class TestIssue1Middleware implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        $request = $request->withAttribute('cookie', ['name' => 'wan2land']);
        return $next($request);
    }
}

class TestIssue1Controller
{
    public function login(ServerRequestInterface $request)
    {
        return "login@Issue1, cookie=" . json_encode($request->getAttribute('cookie', []));
    }
}
