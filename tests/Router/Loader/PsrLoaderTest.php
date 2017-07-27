<?php
namespace Wandu\Router\Loader;

use Closure;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Assertions;
use Wandu\DI\Container;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;
use Wandu\Http\Contracts\ServerParamsInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Parameters\CookieJar;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Parameters\ServerParams;
use Wandu\Http\Parameters\Session;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\Middleware\Parameterify;

class PsrLoaderTest extends TestCase
{
    use Assertions;

    /** @var \Wandu\Router\Loader\PsrLoader $loader */
    protected $loader;

    public function setUp()
    {
        $this->loader = new PsrLoader(new Container());;
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testMiddlewareSuccess()
    {
        static::assertInstanceOf(
            PsrLoaderTestMiddleware::class,
            $this->loader->middleware(PsrLoaderTestMiddleware::class, new ServerRequest())
        );
    }

    public function testMiddlewareFail()
    {
        static::assertException(new HandlerNotFoundException('ThereIsNoClass'), function () {
            $this->loader->middleware('ThereIsNoClass', new ServerRequest());
        });
    }

    public function testExecute()
    {
        static::assertEquals(
            'callFromLoader@StubInLoader',
            $this->loader->execute(PsrLoaderTestController::class, 'callFromLoader', new ServerRequest())
        );
    }

    public function testExecuteWithMagicMethod()
    {
        static::assertEquals(
            '__call->callFromMagicMethod@StubInLoader',
            $this->loader->execute(PsrLoaderTestController::class, 'callFromMagicMethod', new ServerRequest())
        );
    }

    public function testCookieAndSession()
    {
        (new Parameterify())->__invoke(new ServerRequest(), function (ServerRequestInterface $request) {
            static::assertTrue($this->loader->execute(PsrLoaderTestController::class, 'equalServerRequest', $request));
            static::assertTrue($this->loader->execute(PsrLoaderTestController::class, 'equalServerParams', $request));
            static::assertTrue($this->loader->execute(PsrLoaderTestController::class, 'equalQueryParams', $request));
            static::assertTrue($this->loader->execute(PsrLoaderTestController::class, 'equalParsedBody', $request));

            static::assertExceptionMessageEquals(
                'not found parameter named "cookie".',
                function () use ($request) {
                    $this->loader->execute(PsrLoaderTestController::class, 'equalCookie', $request);
                }
            );
            static::assertExceptionMessageEquals(
                'not found parameter named "session".',
                function () use ($request) {
                    $this->loader->execute(PsrLoaderTestController::class, 'equalSession', $request);
                }
            );
        });
    }

    public function testWithRequestAttributes()
    {
        $request = (new ServerRequest)->withAttribute('id', 1)->withAttribute('index', 0);
        $result = $this->loader->execute(PsrLoaderTestController::class, 'withRequestAttributes', $request);
        static::assertEquals([
            'id' => 1,
            'index' => 0,
        ], $result);
    }
    
    public function testResolveWithNullable()
    {
        $result = $this->loader->execute(PsrLoaderTestController::class, 'index', new ServerRequest());
        static::assertNull($result);

        $result = $this->loader->execute(PsrLoaderTestController::class, 'index', (new ServerRequest())->withAttribute('user', ['id' => 1]));
        static::assertEquals(['id' => 1], $result);
    }
}

class PsrLoaderTestMiddleware implements MiddlewareInterface
{
    public function __construct(ServerRequestInterface $request)
    {
    }

    public function __invoke(ServerRequestInterface $request, Closure $next)
    {
    }
}

class PsrLoaderTestController
{
    public function __call($name, $arguments = [])
    {
        return "__call->{$name}@StubInLoader";
    }

    public function callFromLoader()
    {
        return "callFromLoader@StubInLoader";
    }

    public function equalServerRequest(
        ServerRequest $request1,
        ServerRequestInterface $request2
    ) {
        return $request1 === $request2;
    }

    public function equalQueryParams(
        QueryParamsInterface $queryParams,
        QueryParamsInterface $queryParamsInterface
    ) {
        return $queryParams === $queryParamsInterface;
    }

    public function equalServerParams(
        ServerParams $serverParams,
        ServerParamsInterface $serverParamsInterface
    ) {
        return $serverParams === $serverParamsInterface;
    }

    public function equalParsedBody(
        ParsedBody $parsedBody,
        ParsedBodyInterface $parsedBodyInterface
    ) {
        return $parsedBody === $parsedBodyInterface;
    }

    public function equalCookie(
        CookieJar $cookie,
        CookieJarInterface $cookieInterface
    ) {
        return $cookie === $cookieInterface;
    }

    public function equalSession(
        Session $session,
        SessionInterface $sessionInterface
    ) {
        return $session === $sessionInterface;
    }
    
    public function withRequestAttributes($id, $index)
    {
        return [
            'id' => $id,
            'index' => $index,
        ];
    }
    
    public function index($user = null)
    {
        return $user;
    }
}
