<?php
namespace Wandu\DI\Containee;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;
use Wandu\DI\Exception\CannotResolveException;

class AssignTest extends TestCase
{
    use Assertions;

    public function testBindFail()
    {
        // bind interface
        $container = new Container();
        $container->bind(ChainMethodTestAssignIF::class, ChainMethodTestAssign::class);

        static::assertException(
            new CannotResolveException(ChainMethodTestAssignIF::class, 'dep'),
            function () use ($container) {
                $container->get(ChainMethodTestAssign::class);
            }
        );
        static::assertException(
            new CannotResolveException(ChainMethodTestAssignIF::class, 'dep'),
            function () use ($container) {
                $container->get(ChainMethodTestAssignIF::class);
            }
        );

        // bind class directly
        $container = new Container();
        $container->bind(ChainMethodTestAssign::class);

        static::assertException(
            new CannotResolveException(ChainMethodTestAssign::class, 'dep'),
            function () use ($container) {
                $container->get(ChainMethodTestAssign::class);
            }
        );
    }

    public function testBindSuccess()
    {
        // bind interface
        $container = new Container();
        $container->bind(ChainMethodTestAssignIF::class, ChainMethodTestAssign::class)->assign(['dep' => "hello dependency!",]);

        $object = $container->get(ChainMethodTestAssign::class);
        static::assertInstanceOf(ChainMethodTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        $object = $container->get(ChainMethodTestAssignIF::class);
        static::assertInstanceOf(ChainMethodTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        // bind class directly
        $container = new Container();
        $container->bind(ChainMethodTestAssign::class)->assign(['dep' => "hello dependency!",]);

        $object = $container->get(ChainMethodTestAssign::class);
        static::assertInstanceOf(ChainMethodTestAssign::class, $object);
        static::assertSame("hello dependency!", $object->getDep());
    }

    public function testClosureFail()
    {
        $container = new Container();
        $container->closure(ChainMethodTestAssignIF::class, function ($dep) {
            return new ChainMethodTestAssign($dep . ' from closure');
        });

        static::assertException(
            new CannotResolveException(ChainMethodTestAssign::class, 'dep'),
            function () use ($container) {
                $container->get(ChainMethodTestAssign::class);
            }
        );
    }

    public function testClosureSuccess()
    {
        $container = new Container();
        $container->closure(ChainMethodTestAssignIF::class, function ($dep) {
            return new ChainMethodTestAssign($dep . ' from closure');
        })->assign(['dep' => "hello dependency!",]);

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