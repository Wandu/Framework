<?php
namespace Wandu\Router;

use Closure;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\MiddlewareInterface;
use function Wandu\Http\response;

class RouteExecutorTest extends TestCase
{
    public function testExecuteWithoutMiddlewares()
    {
        $executor = new RouteExecutor();

        $response = $executor->execute(
            $this->createRequest('GET', '/'),
            TestRouterExecutorController::class,
            'index',
            []
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('index', $response->getBody()->__toString());
        $this->assertEquals(
            '',
            $response->getHeaderLine('x-via-proxy')
        );
    }

    public function testExecuteWithMiddleware()
    {
        $executor = new RouteExecutor();

        $response = $executor->execute(
            $this->createRequest('GET', '/'),
            TestRouterExecutorController::class,
            'index',
            [
                TestRouterExecutorProxyMiddleware::class,
            ]
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('index', $response->getBody()->__toString());
        $this->assertEquals(
            TestRouterExecutorProxyMiddleware::class,
            $response->getHeaderLine('x-via-proxy')
        );
    }

    public function testExecuteWithPreventedMiddleware()
    {
        $executor = new RouteExecutor();

        $response = $executor->execute(
            $this->createRequest('GET', '/'),
            TestRouterExecutorController::class,
            'index',
            [
                TestRouterExecutorFailMiddleware::class,
            ]
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('Fail', $response->getBody()->__toString());
        $this->assertEquals(400, $response->getStatusCode());
    }
}

class TestRouterExecutorProxyMiddleware implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $next($request);
        return $response->withAddedHeader('X-Via-Proxy', static::class);
    }
}

class TestRouterExecutorFailMiddleware implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        return response()->create("Fail", 400);
    }
}

class TestRouterExecutorController
{
    public function index(ServerRequestInterface $request)
    {
        return response()->create('index');
    }
}
