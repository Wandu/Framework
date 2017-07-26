<?php
namespace Wandu\Router;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Http\Psr\Stream\StringStream;
use Wandu\Router\Exception\RouteNotFoundException;

class ReadmeTest extends TestCase
{
    public function testBasicUsage()
    {
        // section:basic-usage
        $dispatcher = new \Wandu\Router\Dispatcher();
        $route = new \Wandu\Router\RouteCollection();

        $route->get('/', HomeController::class);
        $route->get('/users', UserController::class, 'index');
        $route->get('/users/:id', UserController::class, 'show');

        $request = new ServerRequest('GET', '/'); // PSR7 ServerRequestInterface implementation
        $response = $dispatcher->dispatch($route, $request);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('index', $response->getBody()->__toString());

        $request = new ServerRequest('GET', '/nothing'); // PSR7 ServerRequestInterface implementation
        try {
            $dispatcher->dispatch($route, $request);
        } catch (RouteNotFoundException $e) {
            static::assertEquals('Route not found.', $e->getMessage());
        }
        // endsection
    }
    
    public function testPatternRoutes()
    {
        $dispatcher = new \Wandu\Router\Dispatcher();
        $route = new \Wandu\Router\RouteCollection();

        // section:pattern-routes
        $route->get('/users/:id(\d+)?', UserController::class, 'show');
        $route->get('/users-:id', UserController::class, 'show');
        // endsection

        $response = $dispatcher->dispatch($route, new ServerRequest('GET', '/users/300'));
        static::assertEquals('300', $response->getBody()->__toString());

        $response = $dispatcher->dispatch($route, new ServerRequest('GET', '/users'));
        static::assertEquals('', $response->getBody()->__toString());

        $response = $dispatcher->dispatch($route, new ServerRequest('GET', '/users-300'));
        static::assertEquals('300', $response->getBody()->__toString());
    }
}

// section:basic-usage-controller
class HomeController
{
    public static function index()
    {
        return new Response(200, new StringStream("index"));
    }
}
// endsection

// section:pattern-routes-controller
class UserController
{
    public static function show(ServerRequestInterface $request)
    {
        return new Response(200, new StringStream("{$request->getAttribute('id')}"));
    }
}
// endsection
