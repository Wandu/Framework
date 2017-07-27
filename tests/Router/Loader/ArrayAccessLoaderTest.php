<?php
namespace Wandu\Router\Loader;

use Closure;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Assertions;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class ArrayAccessTest extends TestCase
{
    use Assertions;

    /** @var \Wandu\Router\Loader\ArrayAccessLoader $loader */
    protected $loader;

    public function setUp()
    {
        $this->loader = new ArrayAccessLoader([
            ArrayAccessTestMiddleware::class => new ArrayAccessTestMiddleware("something"),
            ArrayAccessTestSuccessController::class => new ArrayAccessTestSuccessController(),
        ]);
    }

    public function testMiddlewareSuccess()
    {
        $middleware = $this->loader->middleware(ArrayAccessTestMiddleware::class, new ServerRequest());

        static::assertInstanceOf(ArrayAccessTestMiddleware::class, $middleware);
    }

    public function testMiddlewareNotFound()
    {
        static::assertException(new HandlerNotFoundException('ThereIsNoClass'), function () {
            $this->loader->middleware('ThereIsNoClass', new ServerRequest());
        });
    }
    
    public function testExecute()
    {
        static::assertEquals(
            'index@SuccessController',
            $this->loader->execute(ArrayAccessTestSuccessController::class, 'index', new ServerRequest())
        );

        static::assertEquals(
            '__call->unknown@SuccessController',
            $this->loader->execute(ArrayAccessTestSuccessController::class, 'unknown', new ServerRequest())
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
            new HandlerNotFoundException(ArrayAccessTestFailController::class, 'unknown'),
            function () {
                $this->loader->execute(ArrayAccessTestFailController::class, 'unknown', new ServerRequest());
            }
        );
    }
}

class ArrayAccessTestMiddleware implements MiddlewareInterface
{
    public function __construct($something) {}
    public function __invoke(ServerRequestInterface $request, Closure $next) {}
}

class ArrayAccessTestSuccessController
{
    public function __call($name, $arguments = [])
    {
        return "__call->{$name}@SuccessController";
    }

    public function index(ServerRequestInterface $request)
    {
        return "index@SuccessController";
    }
}

class ArrayAccessTestFailController
{
    public function index() {}
}
