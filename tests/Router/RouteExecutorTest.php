<?php
namespace Wandu\Router;

use Closure;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Router\Contracts\MiddlewareInterface;
use function Wandu\Http\response;

class RouteExecutorTest extends TestCase
{
    public function testWithoutMiddlewares()
    {
        $executor = new RouteExecutor();
        
        $response = $executor->execute(
            new Route(RouterExecutorTestController::class, 'index'),
            new ServerRequest('GET', '/')
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('', $response->getBody()->__toString());
        static::assertEquals('', $response->getHeaderLine('x-via-proxy'));
    }

    public function testBypassByMiddleware()
    {
        
        $executor = new RouteExecutor();

        $response = $executor->execute(
            new Route(RouterExecutorTestController::class, 'index', RouterExecutorTestBypassMiddleware::class),
            new ServerRequest('GET', '/')
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('request, bypass-middleware', $response->getBody()->__toString());
        static::assertEquals('response, bypass-middleware', $response->getHeaderLine('x-via-middleware'));
    }

    public function testPreventByMiddlewares()
    {
        $executor = new RouteExecutor();

        $response = $executor->execute(
            new Route(RouterExecutorTestController::class, 'index', RouterExecutorTestPreventMiddleware::class),
            new ServerRequest('GET', '/')
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('Fail', $response->getBody()->__toString());
        static::assertEquals(400, $response->getStatusCode());
    }
}

class RouterExecutorTestBypassMiddleware implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $next($request->withAttribute('viaMiddleware', 'request, bypass-middleware'));
        return $response->withAddedHeader('X-Via-Middleware', 'response, bypass-middleware');
    }
}

class RouterExecutorTestPreventMiddleware implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        return response()->string("Fail", 400);
    }
}

class RouterExecutorTestController
{
    static public function index(ServerRequestInterface $request)
    {
        return response()->string($request->getAttribute('viaMiddleware') ?? '');
    }
}
