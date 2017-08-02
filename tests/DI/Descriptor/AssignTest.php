<?php
namespace Wandu\DI\Descriptor;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;
use Wandu\DI\Exception\CannotResolveException;
use ReflectionClass;

class AssignTest extends TestCase
{
    use Assertions;

    public function testBindFail()
    {
        // bind interface
        $container = new Container();
        $container->bind(ChainMethodTestAssignIF::class, ChainMethodTestAssign::class);

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ChainMethodTestAssign::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(ChainMethodTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ChainMethodTestAssignIF::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(ChainMethodTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        // bind class directly
        $container = new Container();
        $container->bind(ChainMethodTestAssign::class);

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ChainMethodTestAssign::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(ChainMethodTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        // assign unknown
        $container = new Container();
        $container->bind(ChainMethodTestAssign::class)->assign('dep', 'unknown');

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ChainMethodTestAssign::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(ChainMethodTestAssign::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );
    }

    public function testBindSuccess()
    {
        // bind interface
        $container = new Container();
        $container->instance('dep_dependency', 'hello dependency!');
        $container->bind(ChainMethodTestAssignIF::class, ChainMethodTestAssign::class)->assign('dep', "dep_dependency");

        $object = $container->get(ChainMethodTestAssign::class);
        static::assertInstanceOf(ChainMethodTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        $object = $container->get(ChainMethodTestAssignIF::class);
        static::assertInstanceOf(ChainMethodTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        // bind class directly
        $container = new Container();
        $container->instance('dep_dependency', 'hello dependency!');
        $container->bind(ChainMethodTestAssign::class)->assign('dep', 'dep_dependency');

        $object = $container->get(ChainMethodTestAssign::class);
        static::assertInstanceOf(ChainMethodTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());
    }

    public function testBindClosureFail()
    {
        $container = new Container();
        $container->bind(ChainMethodTestAssign::class, function ($dep) {
            return new ChainMethodTestAssign($dep . ' from closure');
        });

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(ChainMethodTestAssign::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(__LINE__ - 12, $exception->getLine());
    }

    public function testClosureSuccess()
    {
        $container = new Container();
        $container->instance('dep_dependency', 'hello dependency!');
        $container->bind(ChainMethodTestAssignIF::class, function ($dep) {
            return new ChainMethodTestAssign($dep . ' from closure');
        })->assign('dep', "dep_dependency");

        $object = $container->get(ChainMethodTestAssignIF::class);
        static::assertInstanceOf(ChainMethodTestAssign::class, $object);
        static::assertSame("hello dependency! from closure", $object->getDep());
    }
}

interface ChainMethodTestDependInterface {}
class ChainMethodTestDepend implements ChainMethodTestDependInterface {}

interface ChainMethodTestAssignIF {}
class ChainMethodTestAssign implements ChainMethodTestAssignIF
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
