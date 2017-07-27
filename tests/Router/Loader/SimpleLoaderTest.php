<?php
namespace Wandu\Router\Loader;

use Closure;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Assertions;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class SimpleLoaderTest extends TestCase
{
    use Assertions;

    /** @var \Wandu\Router\Loader\SimpleLoader $loader */
    protected $loader;

    public function setUp()
    {
        $this->loader = new SimpleLoader();
    }

    public function testMiddlewareSuccess()
    {
        $middleware = $this->loader->middleware(SimpleLoaderTestMiddlewareSuccess::class, new ServerRequest());
        
        static::assertInstanceOf(SimpleLoaderTestMiddlewareSuccess::class, $middleware);
    }

    public function testMiddlewareNotFound()
    {
        static::assertException(new HandlerNotFoundException('ThereIsNoClass'), function () {
            $this->loader->middleware('ThereIsNoClass', new ServerRequest());
        });
    }

    /**
     * @expectedException \TypeError
     */
    public function testMiddlewareFail()
    {
        $this->loader->middleware(SimpleLoaderTestMiddlewareFail::class, new ServerRequest());
    }

    public function testExecute()
    {
        static::assertEquals(
            'index@SuccessController',
            $this->loader->execute(SimpleLoaderTestSuccessController::class, 'index', new ServerRequest())
        );

        static::assertEquals(
            '__callStatic->unknown@SuccessController',
            $this->loader->execute(SimpleLoaderTestSuccessController::class, 'unknown', new ServerRequest())
        );
    }

    public function testExecuteNotFound()
    {
        static::assertException(
            new HandlerNotFoundException('UnknownController', 'unknown'),
            function () {
                $this->loader->execute('UnknownController', 'unknown', new ServerRequest());
            }
        );

        static::assertException(
            new HandlerNotFoundException(SimpleLoaderTestFailController::class, 'unknown'),
            function () {
                $this->loader->execute(SimpleLoaderTestFailController::class, 'unknown', new ServerRequest());
            }
        );
    }

    /**
     * @expectedException \PHPUnit\Framework\Error\Deprecated
     */
    public function testExecuteFail()
    {
        $this->loader->execute(SimpleLoaderTestFailController::class, 'nonStatic', new ServerRequest());
    }
}

class SimpleLoaderTestMiddlewareSuccess implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, Closure $next) {}
}

class SimpleLoaderTestMiddlewareFail implements MiddlewareInterface
{
    public function __construct(ServerRequestInterface $request) {}
    public function __invoke(ServerRequestInterface $request, Closure $next) {}
}

class SimpleLoaderTestSuccessController
{
    static public function __callStatic($name, $arguments = [])
    {
        return "__callStatic->{$name}@SuccessController";
    }

    static public function index(ServerRequestInterface $request)
    {
        return "index@SuccessController";
    }
}

class SimpleLoaderTestFailController
{
    public function nonStatic() {}
}
