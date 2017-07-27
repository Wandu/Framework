<?php
namespace Wandu\Router;

use Closure;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Assertions;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Http\Psr\Stream\StringStream;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Contracts\Routable;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Loader\SimpleLoader;
use Wandu\Router\Responsifier\NullResponsifier;

class RouteCollectionTest extends TestCase
{
    use Assertions;
    
    public function testBasic()
    {
        $router = new RouteCollection;

        $router->get('/admin', "TestSimpleController", 'index');
        $router->post('/admin', "TestSimpleController", 'action');

        static::assertEquals(
            [
                [['GET', 'HEAD'], '/admin', new Route("TestSimpleController", "index"), ],
                [['POST'], '/admin', new Route("TestSimpleController", "action"), ],
            ],
            $router->toArray()
        );
    }

    public function testGroup()
    {
        $router = new RouteCollection;

        $router->get('/', "TestGroupController", 'index');
        $router->group([
            'prefix' => 'admin',
            'middleware' => ["TestGroupMiddleware"],
            'domain' => 'admin.wandu.github.io',
        ], function (RouteCollection $router) {
            $router->get('', "TestGroupController2", 'index');
            $router->post('/', "TestGroupController2", 'store');
            $router->get('users/:user', "TestGroupController2", 'show');
        });

        static::assertEquals(
            [
                [['GET', 'HEAD'], '/', new Route("TestGroupController", "index"), ],
                [
                    ['GET', 'HEAD'],
                    '/admin',
                    new Route("TestGroupController2", "index", ["TestGroupMiddleware"], ['admin.wandu.github.io']),
                ],
                [
                    ['POST'],
                    '/admin',
                    new Route("TestGroupController2", "store", ["TestGroupMiddleware"], ['admin.wandu.github.io']),
                ],
                [
                    ['GET', 'HEAD'],
                    '/admin/users/:user',
                    new Route("TestGroupController2", "show", ["TestGroupMiddleware"], ['admin.wandu.github.io']),
                ],
            ],
            $router->toArray()
        );
    }

    public function testDispatcherSimplePath()
    {
        $router = new RouteCollection();

        $router->get('/', RouteCollectionTestHomeController::class);
        $router->get('/users', RouteCollectionTestUserController::class, 'index');
        $router->post('/users', RouteCollectionTestUserController::class, 'store');
        $router->get('/users/error', RouteCollectionTestUserController::class, 'wrongHandler');

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/')
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('[GET] index@HomeController', $response->getBody()->__toString());

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/users')
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('[GET] index@UserController', $response->getBody()->__toString());

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('POST', '/users')
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('[POST] store@UserController', $response->getBody()->__toString());

        // not allowed!
        static::assertException(new RouteNotFoundException(), function () use ($router) {
            $router->dispatch(
                new SimpleLoader(),
                new NullResponsifier(),
                new ServerRequest('GET', '/unknown')
            );
        });
        static::assertException(new MethodNotAllowedException(), function () use ($router) {
            $router->dispatch(
                new SimpleLoader(),
                new NullResponsifier(),
                new ServerRequest('PUT', '/users')
            );
        });
        
        // handler not found
        static::assertException(
            new HandlerNotFoundException(RouteCollectionTestUserController::class, 'wrongHandler'),
            function () use ($router) {
                $router->dispatch(
                    new SimpleLoader(),
                    new NullResponsifier(),
                    new ServerRequest('GET', '/users/error')
                );
            }
        );
    }

    public function testDispatcherPatternedPath()
    {
        $router = new RouteCollection();

        $router->get('/users/:id(\d+)', RouteCollectionTestUserController::class, 'show');
        
        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/users/100')
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals('[GET] show:100@UserController', $response->getBody()->__toString());
        
        // not allowed!
        static::assertException(new RouteNotFoundException(), function () use ($router) {
            $router->dispatch(
                new SimpleLoader(),
                new NullResponsifier(),
                new ServerRequest('GET', '/users/wan2land')
            );
        });
    }
    
    public function testDispatchDomains()
    {
        $router = new RouteCollection();

        $router->createRoute(['GET'], '/', RouteCollectionTestHomeController::class, 'index');
        $router->createRoute(['GET'], '/something', RouteCollectionTestHomeController::class, 'something');
        $router->domain(["admin.wandu.io", "admin.wandu.dev"], function (Routable $router) {
            $router->createRoute(['GET'], '/', RouteCollectionTestUserController::class, 'index');
            $router->createRoute(['GET'], '/:id', RouteCollectionTestUserController::class, 'show');
        });

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/', null, ['host' => 'wandu.io'])
        );
        static::assertEquals('[GET] index@HomeController', $response->getBody()->__toString());

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/something', null, ['host' => 'wandu.io'])
        );
        static::assertEquals('[GET] something@HomeController', $response->getBody()->__toString());

        static::assertException(new RouteNotFoundException(), function () use ($router) {
            $router->dispatch(
                new SimpleLoader(),
                new NullResponsifier(),
                new ServerRequest('GET', '/otherthing', null, ['host' => 'wandu.io'])
            );
        });         

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/', null, ['host' => 'admin.wandu.io'])
        );
        static::assertEquals('[GET] index@UserController', $response->getBody()->__toString());

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/something', null, ['host' => 'admin.wandu.io'])
        );
        static::assertEquals('[GET] show:something@UserController', $response->getBody()->__toString());

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/', null, ['host' => 'admin.wandu.dev'])
        );
        static::assertEquals('[GET] index@UserController', $response->getBody()->__toString());

        $response = $router->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/something', null, ['host' => 'admin.wandu.dev'])
        );
        static::assertEquals('[GET] show:something@UserController', $response->getBody()->__toString());
    }

    public function testPrefix()
    {
        $expected = new RouteCollection();

        $expected->get('/', "HomeController", 'index');
        $expected->get('/users/', "UserController", 'index');
        $expected->post('/users/', "UserController", 'store');
        $expected->get('/users/:id', "UserController", 'show');


        $router1 = new RouteCollection();
        $router1->get('/', "HomeController", 'index');
        $router1->prefix('users', function (RouteCollection $router) {
            $router->get('/', "UserController", 'index');
            $router->post('/', "UserController", 'store');
            $router->get('/:id', "UserController", 'show');
        });

        $router2 = new RouteCollection();
        $router2->get('/', "HomeController", 'index');
        $router2->prefix('/users', function (RouteCollection $router) {
            $router->get('/', "UserController", 'index');
            $router->post('/', "UserController", 'store');
            $router->get('/:id', "UserController", 'show');
        });

        static::assertEquals($expected->toArray(), $router1->toArray());
        static::assertEquals($expected->toArray(), $router2->toArray());
    }

    public function testMiddlewares()
    {
        $routes = new RouteCollection();

        $routes->get('/', "HomeController", 'index');
        $routes->middleware(RouteCollectionTestAuthMiddleware::class, function (Routable $routes) {
            $routes->get('/users/', RouteCollectionTestUserController::class, 'index');
            $routes->post('/users/', RouteCollectionTestUserController::class, 'store');
            $routes->get('/users/:id', RouteCollectionTestUserController::class, 'show');
        });

        $response = $routes->dispatch(
            new SimpleLoader(),
            new NullResponsifier(),
            new ServerRequest('GET', '/users/300')
        );
        static::assertEquals('[GET] show:300@UserController and middleware!', $response->getBody()->__toString());
    }
    
    public function provideShortenMethodsTest()
    {
        return [
            ['get', ['GET', 'HEAD'], ['POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH', 'CUSTOM_METHOD'], ],
            ['post', ['POST'], ['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'PATCH', 'CUSTOM_METHOD'], ],
            ['put', ['PUT'], ['GET', 'HEAD', 'POST', 'DELETE', 'OPTIONS', 'PATCH', 'CUSTOM_METHOD'], ],
            ['delete', ['DELETE'], ['GET', 'HEAD', 'POST', 'PUT', 'OPTIONS', 'PATCH', 'CUSTOM_METHOD'], ],
            ['options', ['OPTIONS'], ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'PATCH', 'CUSTOM_METHOD'], ],
            ['patch', ['PATCH'], ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'CUSTOM_METHOD'], ],
            ['any', ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'], ['CUSTOM_METHOD'], ],
        ];
    }

    /**
     * @dataProvider provideShortenMethodsTest
     */
    public function testShortenMethods($method, $allowedMethods, $disallowedMethods)
    {
        $routes = new RouteCollection();
        $routes->{$method}('/', RouteCollectionTestHomeController::class, 'index');

        // success
        foreach ($allowedMethods as $method) {
            $response = $routes->dispatch(
                new SimpleLoader(),
                new NullResponsifier(),
                new ServerRequest($method, '/')
            );
            static::assertEquals("[{$method}] index@HomeController", $response->getBody()->__toString());
        }
        foreach ($disallowedMethods as $method) {
            static::assertException(new MethodNotAllowedException(), function () use ($routes, $method) {
                $routes->dispatch(
                    new SimpleLoader(),
                    new NullResponsifier(),
                    new ServerRequest($method, '/')
                );                
            });
        }
    }
}

class RouteCollectionTestHomeController
{
    static public function index(ServerRequestInterface $request)
    {
        return new Response(200, new StringStream("[{$request->getMethod()}] index@HomeController"));
    }

    static public function something(ServerRequestInterface $request)
    {
        return new Response(200, new StringStream("[{$request->getMethod()}] something@HomeController"));
    }
}

class RouteCollectionTestUserController
{
    static public function index(ServerRequestInterface $request)
    {
        return new Response(200, new StringStream("[{$request->getMethod()}] index@UserController"));
    }

    static public function store(ServerRequestInterface $request)
    {
        return new Response(200, new StringStream("[{$request->getMethod()}] store@UserController"));
    }

    static public function show(ServerRequestInterface $request)
    {
        return new Response(200, new StringStream("[{$request->getMethod()}] show:{$request->getAttribute('id')}@UserController"));
    }
}

class RouteCollectionTestAuthMiddleware implements MiddlewareInterface 
{
    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $next($request);
        $response->getBody()->write(" and middleware!");
        return $response;
    }
}
