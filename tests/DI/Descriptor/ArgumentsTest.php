<?php
namespace Wandu\DI\Descriptor;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;
use Wandu\DI\Exception\CannotResolveException;
use ReflectionClass;

class ArgumentsTest extends TestCase
{
    use Assertions;

    public function testBindFail()
    {
        // bind interface
        $container = new Container();
        $container->bind(ArgumentsTestAssignIF::class, ArgumentsTestAssign::class);

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ArgumentsTestAssign::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(ArgumentsTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ArgumentsTestAssignIF::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(ArgumentsTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        // bind class directly
        $container = new Container();
        $container->bind(ArgumentsTestAssign::class);

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ArgumentsTestAssign::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(ArgumentsTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );
    }

    public function testBindSuccess()
    {
        // bind interface
        $container = new Container();
        $container->bind(ArgumentsTestAssignIF::class, ArgumentsTestAssign::class)->arguments(['dep' => "hello dependency!"]);

        $object = $container->get(ArgumentsTestAssign::class);
        static::assertInstanceOf(ArgumentsTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        $object = $container->get(ArgumentsTestAssignIF::class);
        static::assertInstanceOf(ArgumentsTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        // bind class directly
        $container = new Container();
        $container->bind(ArgumentsTestAssign::class)->arguments(['dep' => "hello dependency!"]);

        $object = $container->get(ArgumentsTestAssign::class);
        static::assertInstanceOf(ArgumentsTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());
    }

    public function testBindClosureFail()
    {
        $container = new Container();
        $container->bind(ArgumentsTestAssignIF::class, function ($dep) {
            return new ArgumentsTestAssign($dep . ' from closure');
        });

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ArgumentsTestAssignIF::class);
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
        $container->bind(ArgumentsTestAssignIF::class, function ($dep) {
            return new ArgumentsTestAssign($dep . ' from closure');
        })->arguments(['dep' => "hello dependency!"]);

        $object = $container->get(ArgumentsTestAssignIF::class);
        static::assertInstanceOf(ArgumentsTestAssign::class, $object);
        static::assertSame("hello dependency! from closure", $object->getDep());
    }
}

interface ArgumentsTestDependInterface {}
class ArgumentsTestDepend implements ArgumentsTestDependInterface {}

interface ArgumentsTestAssignIF {}
class ArgumentsTestAssign implements ArgumentsTestAssignIF
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
