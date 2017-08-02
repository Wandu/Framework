<?php
namespace Wandu\DI;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Assertions;
use ReflectionClass;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Psr\ServerRequest;

class AutoResolveTest extends TestCase
{
    use Assertions;
    
    public function testBind()
    {
        $container = new Container();

        $container->bind(AutoResolveTestDependInterface::class, AutoResolveTestDepend::class);

        $instance1 = $container->get(AutoResolveTestDepend::class);
        $instance2 = $container->get(AutoResolveTestDependInterface::class);

        static::assertInstanceOf(AutoResolveTestDepend::class, $instance1);
        static::assertInstanceOf(AutoResolveTestDependInterface::class, $instance1);
        
        static::assertSame($instance1, $instance2);
    }
    
    public function testResolveException()
    {
        $container = new Container();

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(AutoResolveTestExceptionDepth1::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('unknown', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(AutoResolveTestExceptionDepth1::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(AutoResolveTestDepth2::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('unknown', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(AutoResolveTestExceptionDepth1::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );
    }
    
    public function testCascadeResolve()
    {
        $container = new Container();

        $count = 0;
        
        $request = new ServerRequest();
        $request = $request->withParsedBody(['abc' => 'def']);
        
        $container->with([
            ServerRequestInterface::class => $request,
        ])->call(function (ServerRequestInterface $req, ParsedBody $parsedBody) use (&$count, $request) {
            static::assertSame($request, $req);
            $count++;
        });
        
        static::assertEquals(1, $count);
    }
}

interface AutoResolveTestDependInterface {}
class AutoResolveTestDepend implements AutoResolveTestDependInterface {}

class AutoResolveTestExceptionDepth1
{
    public function __construct(UnknownDepend $unknown)
    {
    }
}

class AutoResolveTestDepth2
{
    public function __construct(AutoResolveTestExceptionDepth1 $depth1)
    {
    }
}
