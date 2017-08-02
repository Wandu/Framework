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

    public function testAssignFailByAssigningUnknown()
    {
        // assign unknown
        $container = new Container();
        $container->bind(AssignTestClass::class)->assign('dep', 'unknown');

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(AssignTestClass::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(AssignTestClass::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );
    }

    public function testAssignByBind()
    {
        // bind interface
        $container = new Container();
        $container->instance('dep_dependency', 'hello dependency!');
        $container->bind(AssignTestClassIF::class, AssignTestClass::class)->assign('dep', "dep_dependency");

        $object = $container->get(AssignTestClass::class);
        static::assertInstanceOf(AssignTestClass::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        $object = $container->get(AssignTestClassIF::class);
        static::assertInstanceOf(AssignTestClass::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        // bind class directly
        $container = new Container();
        $container->instance('dep_dependency', 'hello dependency!');
        $container->bind(AssignTestClass::class)->assign('dep', 'dep_dependency');

        $object = $container->get(AssignTestClass::class);
        static::assertInstanceOf(AssignTestClass::class, $object);
        static::assertSame("hello dependency!", $object->getDep());
    }

    public function testAssignFailByClosure()
    {
        $container = new Container();
        $container->bind(AssignTestClass::class, function ($dep) {
            return new AssignTestClass($dep . ' from closure');
        });

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->get(AssignTestClass::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('dep', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(__LINE__ - 12, $exception->getLine());
    }

    public function testAssignSuccessByClosure()
    {
        $container = new Container();
        $container->instance('dep_dependency', 'hello dependency!');
        $container->bind(AssignTestClassIF::class, function ($dep) {
            return new AssignTestClass($dep . ' from closure');
        })->assign('dep', "dep_dependency");

        $object = $container->get(AssignTestClassIF::class);
        static::assertInstanceOf(AssignTestClass::class, $object);
        static::assertSame("hello dependency! from closure", $object->getDep());
    }
}

interface AssignTestClassIF {}
class AssignTestClass implements AssignTestClassIF
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
