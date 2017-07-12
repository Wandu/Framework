<?php
namespace Wandu\Router;

use Wandu\Assertions;

class RouteTest extends TestCase
{
    use Assertions;
    
    public function provideRoutesForTestConstructor()
    {
        return [
            [
                new Route('HomeController', 'index', 'Middleware1', 'wandu.github.io'),
            ],
            [
                new Route('HomeController', 'index', ['Middleware1'], ['wandu.github.io']),
            ],
            [
                (new Route('HomeController', 'index'))->middleware('Middleware1')->domains('wandu.github.io'), // by fluent
            ],
            [
                (new Route('HomeController', 'index'))->middleware(['Middleware1'])->domains(['wandu.github.io']), // by fluent
            ]
        ];
    }

    /**
     * @dataProvider provideRoutesForTestConstructor
     * @param \Wandu\Router\Route $route
     */
    public function testConstructor(Route $route)
    {
        static::assertEquals('HomeController', $route->getClassName());
        static::assertEquals('index', $route->getMethodName());
        static::assertEquals(['Middleware1'], $route->getMiddlewares()); // always array
        static::assertEquals(['wandu.github.io'], $route->getDomains()); // always array
    }
    
    public function testMiddlewareFluent()
    {
        $route = new Route('HomeController', 'index', ['Middleware1']);
        $route->middleware('Middleware2');
        
        static::assertEquals(['Middleware1', 'Middleware2'], $route->getMiddlewares());


        $route = new Route('HomeController', 'index', ['Middleware1']);
        $route->middleware('Middleware2', true);

        static::assertEquals(['Middleware2'], $route->getMiddlewares());
    }

    public function testDomainFluent()
    {
        $route = new Route('HomeController', 'index', [], ['wandu.github.io']);
        $route->domains(['wandu2.github.io']); // always overwrite

        static::assertEquals(['wandu2.github.io'], $route->getDomains());


        $route = new Route('HomeController', 'index', [], ['wandu.github.io']);
        $route->domains(); // always overwrite

        static::assertEquals([], $route->getDomains());
    }
}
