<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Assertions;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Router\Contracts\Dispatchable;

class DispatcherTest extends TestCase 
{
    use Assertions;

    public function tearDown()
    {
        Mockery::close();
    }
    
    public function provideMethodSpoofingParams()
    {
        return [
            [ new Dispatcher(), 'POST', ], // default = disabled
            [ new Dispatcher(null, null, null, ['method_spoofing_enabled' => false]), 'POST', ],
            [ new Dispatcher(null, null, null, ['method_spoofing_enabled' => true]), 'PUT', ],
        ];
    }

    /**
     * @dataProvider provideMethodSpoofingParams
     * @param \Wandu\Router\Dispatcher $dispatcher
     * @param string $method
     */
    public function testMethodSpoofingDefault($dispatcher, $method)
    {
        $routes = Mockery::mock(Dispatchable::class);
        $routes->shouldReceive('dispatch')
            ->with(Mockery::any(), Mockery::any(), Mockery::on(function (ServerRequestInterface $request) use ($method) {
                static::assertEquals($method, $request->getMethod());
                return true;
            }))
            ->once();

        $request = new ServerRequest('POST', '/', null, [], '1.1', [], [], ['_method' => 'put']);
        $dispatcher->dispatch($routes, $request);
    }

    public function provideMethodOverrideParams()
    {
        return [
            [ new Dispatcher(), 'PUT', ], // default = enabled
            [ new Dispatcher(null, null, null, ['method_override_enabled' => false]), 'POST', ],
            [ new Dispatcher(null, null, null, ['method_override_enabled' => true]), 'PUT', ],
        ];
    }

    /**
     * @dataProvider provideMethodOverrideParams
     * @param \Wandu\Router\Dispatcher $dispatcher
     * @param string $method
     */
    public function testMethodOverrideDefault($dispatcher, $method)
    {
        $routes = Mockery::mock(Dispatchable::class);
        $routes->shouldReceive('dispatch')
            ->with(Mockery::any(), Mockery::any(), Mockery::on(function (ServerRequestInterface $request) use ($method) {
                static::assertEquals($method, $request->getMethod());
                return true;
            }))
            ->once();

        $request = new ServerRequest('POST', '/', null, ['X-Http-Method-Override' => 'put']);
        $dispatcher->dispatch($routes, $request);
    }
    
    public function testCreateRouteCollection()
    {
        $dispatcher = new Dispatcher();
        $routes = $dispatcher->createRouteCollection();
        $routes->get('/', 'HomeController');
        
        static::assertEquals([
            [
                ['GET', 'HEAD'],
                '/',
                new Route('HomeController', 'index'),
            ]
        ], $routes->toArray());

        $dispatcher = new Dispatcher(null, null, null, [
            'defined_domains' => 'admin.wandu.io',
            'defined_prefix' => 'admin',
            'defined_middlewares' => 'AuthMiddleware',
        ]);
        $routes = $dispatcher->createRouteCollection();
        $routes->get('/', 'HomeController');

        static::assertEquals([
            [
                ['GET', 'HEAD'],
                '/admin',
                new Route('HomeController', 'index', ['AuthMiddleware'], ['admin.wandu.io']),
            ]
        ], $routes->toArray());

        $dispatcher = new Dispatcher(null, null, null, [
            'defined_domains' => ['admin.wandu.io', 'admin.wandu.dev', ],
            'defined_prefix' => 'admin',
            'defined_middlewares' => ['Sessionify', 'AuthMiddleware', ],
        ]);
        $routes = $dispatcher->createRouteCollection();
        $routes->get('/', 'HomeController');

        static::assertEquals([
            [
                ['GET', 'HEAD'],
                '/admin',
                new Route('HomeController', 'index', ['Sessionify', 'AuthMiddleware'], ['admin.wandu.io', 'admin.wandu.dev', ]),
            ]
        ], $routes->toArray());
    }
}
