<?php
namespace Wandu\Router;

use Wandu\Router\MapperInterface;
use Wandu\Router\stubs\AdminController;
use Psr\Http\Message\ServerRequestInterface;
use Mockery;
use PHPUnit_Framework_TestCase;
use ArrayObject;
use Wandu\Router\Stubs\AuthMiddleware;

class RouterTest extends PHPUnit_Framework_TestCase
{
    /** @var Router */
    protected $router;

    public function setUp()
    {
        $mockMapper = Mockery::mock(MapperInterface::class);
        $mockMapper
            ->shouldReceive('mapHandler')
            ->with('index@AdminController')
            ->andReturn([new AdminController, 'index']);
        $mockMapper
            ->shouldReceive('mapHandler')
            ->with('action@AdminController')
            ->andReturn([new AdminController, 'action']);
        $mockMapper
            ->shouldReceive('mapMiddleware')
            ->with('AuthMiddleware')
            ->andReturn(new AuthMiddleware);

        $this->router = new Router($mockMapper);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testDefaultAction()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/');
        $mockRequest->shouldReceive('setArguments')->with([
        ]);

        $this->router->createRoute('GET', '', 'index@AdminController');
        $this->assertEquals('index@AdminController string', $this->router->dispatch($mockRequest));
    }

    public function testDispatch()
    {
        $mockGetRequest = Mockery::mock(ServerRequestInterface::class);
        $mockGetRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockGetRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockGetRequest->shouldReceive('getUri->getPath')->andReturn('/');
        $mockGetRequest->shouldReceive('setArguments')->with([
        ]);

        $mockPostRequest = Mockery::mock(ServerRequestInterface::class);
        $mockPostRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockPostRequest->shouldReceive('getMethod')->andReturn('POST');
        $mockPostRequest->shouldReceive('getUri->getPath')->andReturn('/');
        $mockPostRequest->shouldReceive('setArguments')->with([
        ]);

        $getCalledCount = 0;
        $postCalledCount = 0;

        $this->router->get(
            '',
            function (ServerRequestInterface $req) use (&$getCalledCount) {
                $getCalledCount++;
                return 'get';
            },
            [function (ServerRequestInterface $req, callable $next) {
                return $next($req) . ' getMiddleware';
            }]
        );

        $this->router->post(
            '',
            function (ServerRequestInterface $req) use (&$postCalledCount) {
                $postCalledCount++;
                return 'post';
            },
            [function (ServerRequestInterface $req, callable $next) {
                return $next($req) . ' postMiddleware';
            }]
        );

        $this->assertEquals('get getMiddleware', $this->router->dispatch($mockGetRequest));

        $this->assertEquals(1, $getCalledCount);
        $this->assertEquals(0, $postCalledCount);

        $this->assertEquals('post postMiddleware', $this->router->dispatch($mockPostRequest));

        $this->assertEquals(1, $getCalledCount);
        $this->assertEquals(1, $postCalledCount);
    }

    public function testDispatchWithArguments()
    {

        $getMock = Mockery::mock(ServerRequestInterface::class);
        $getMock->shouldReceive('getParsedBody')->andReturn([]);
        $getMock->shouldReceive('getMethod')->andReturn('GET');
        $getMock->shouldReceive('getUri->getPath')->andReturn('/jicjjang/hello');
        $getMock->shouldReceive('withAttribute')->andReturn($getMock);

        $this->router->get(
            '/{name}/{message}',
            function (ServerRequestInterface $req) {
                return 'get';
            },
            [function (ServerRequestInterface $req, callable $next) {
                return $next($req) . ' getMiddleware';
            }]
        );

        $this->assertEquals('get getMiddleware', $this->router->dispatch($getMock));
    }

    public function testAnyMethod()
    {
        $anyMock = Mockery::mock(ServerRequestInterface::class);
        $anyMock->shouldReceive('getParsedBody')->andReturn([]);
        $anyMock->shouldReceive('getMethod')->andReturn('GET');
        $anyMock->shouldReceive('getUri->getPath')->andReturn('/');
        $anyMock->shouldReceive('setArguments')->with([
        ]);

        $this->router->any('', function () {
            return 'any';
        });

        $this->assertEquals('any', $this->router->dispatch($anyMock));
        $this->assertEquals('any', $this->router->dispatch($anyMock));
    }

    public function testExecuteWithController()
    {
        $getMock = Mockery::mock(ServerRequestInterface::class);
        $getMock->shouldReceive('getParsedBody')->andReturn([]);
        $getMock->shouldReceive('getMethod')->andReturn('GET');
        $getMock->shouldReceive('getUri->getPath')->andReturn('/');
        $getMock->shouldReceive('setArguments')->with([
        ]);

        $this->router->get('/', "action@AdminController", ["AuthMiddleware"]);

        $this->assertEquals('action@AdminController string middleware~', $this->router->dispatch($getMock));
    }

    public function testGroup()
    {
        $router = $this->router;

        $this->router->get('', function () { return '/!'; });
        $this->router->group('/hello', function () use ($router) {
            $this->router->get('', function () { return '/hello!'; });
            $this->router->get('/world', function () { return '/hello/world!'; });
            $this->router->get('/another', function () { return '/hello/another!'; });
        });

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/hello/world');

        $this->assertEquals('/hello/world!', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/hello/world');

        $this->assertEquals('/hello/world!', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/hello');

        $this->assertEquals('/hello!', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/');

        $this->assertEquals('/!', $this->router->dispatch($mockRequest));
    }

    public function testGroupWithMiddleware()
    {
        $router = $this->router;

        $this->router->get('', function () { return '/!'; });
        $this->router->group([
            'prefix' => '/hello',
            'middleware' => [function ($request, $next) { return '[m]' . $next($request); }]
        ], function () use ($router) {
            $this->router->get('', function () { return '/hello!'; });
        });

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/hello');

        $this->assertEquals('[m]/hello!', $this->router->dispatch($mockRequest));
    }

    public function testMultipleGroup()
    {
        $router = $this->router;

        $this->router->get('', 'index@Main');
        $this->router->group([
            'prefix' => '/admin',
            'middleware' => ['auth@Admin']
        ], function () use ($router) {
            $this->router->group([
                'prefix' => '/member',
                'middleware' => ['member@Admin']
            ], function () use ($router) {
                $this->router->get('/', 'index@AdminMember');
                $this->router->get('', 'index@AdminMember');
            });
        });

        $routes = $this->router->getRoutes();

        $this->assertTrue(isset($routes['GET,HEAD/']));
        $this->assertTrue(isset($routes['GET,HEAD/admin/member']));
        $this->assertTrue(isset($routes['GET,HEAD/admin/member/']));
    }

    public function testVirtualMethod()
    {
        $this->router->put('', function () {
            return 'call put!';
        });

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/');
        $mockRequest->shouldReceive('getMethod')->andReturn('POST');
        $mockRequest->shouldReceive('getParsedBody')->andReturn([
            '_method' => 'put'
        ]);

        $this->assertEquals('call put!', $this->router->dispatch($mockRequest));
    }

    public function testGroupSlashSensitive()
    {
        $router = $this->router;

        $this->router->get('', function () { return '/!'; });
        $this->router->group('/hello', function () use ($router) {
            $this->router->get('', function () { return '/hello!'; });
            $this->router->get('/', function () { return '/hello/!'; });
            $this->router->get('///abc', function () { return '/hello///abc!'; });
        });

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/hello');

        $this->assertEquals('/hello!', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/hello/');

        $this->assertEquals('/hello/!', $this->router->dispatch($mockRequest));

        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn([]);
        $mockRequest->shouldReceive('getMethod')->andReturn('GET');
        $mockRequest->shouldReceive('getUri->getPath')->andReturn('/hello///abc');

        $this->assertEquals('/hello///abc!', $this->router->dispatch($mockRequest));
    }
}
