<?php
namespace Wandu\DI\Descriptor;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;
use Wandu\DI\Exception\CannotResolveException;
use ReflectionClass;

class WithTest extends TestCase
{
    use Assertions;

    public function testBindFail()
    {
        // bind interface
        $container = new Container();
        $container->bind(WithTestAssignIF::class, WithTestAssign::class);

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(WithTestAssign::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(WithTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(WithTestAssignIF::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(WithTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        // bind class directly
        $container = new Container();
        $container->bind(WithTestAssign::class);

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(WithTestAssign::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(WithTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );
    }

    public function testBindSuccess()
    {
        // bind interface
        $container = new Container();
        $container->bind(WithTestAssignIF::class, WithTestAssign::class)->with('dep', "hello dependency!");

        $object = $container->get(WithTestAssign::class);
        static::assertInstanceOf(WithTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        $object = $container->get(WithTestAssignIF::class);
        static::assertInstanceOf(WithTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        // bind class directly
        $container = new Container();
        $container->bind(WithTestAssign::class)->with('dep', "hello dependency!");

        $object = $container->get(WithTestAssign::class);
        static::assertInstanceOf(WithTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());
    }

    public function testBindClosureFail()
    {
        $container = new Container();
        $container->bind(WithTestAssignIF::class, function ($dep) {
            return new WithTestAssign($dep . ' from closure');
        });

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(WithTestAssignIF::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            __LINE__ - 13,
            $exception->getLine()
        );
    }

    public function testBindClosureSuccess()
    {
        $container = new Container();
        $container->bind(WithTestAssignIF::class, function ($dep) {
            return new WithTestAssign($dep . ' from closure');
        })->with('dep', "hello dependency!");

        $object = $container->get(WithTestAssignIF::class);
        static::assertInstanceOf(WithTestAssign::class, $object);
        static::assertSame("hello dependency! from closure", $object->getDep());
    }
}

interface WithTestDependInterface {}
class WithTestDepend implements WithTestDependInterface {}

interface WithTestAssignIF {}
class WithTestAssign implements WithTestAssignIF
{
    protected $dep;
    public function __construct($dep)
    {
        $this->dep = $dep;
    }

    public function getDep()
    {
        return $this->dep;
    }
}
