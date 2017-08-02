<?php
namespace Wandu\DI\Descriptor;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;

class AssignValueTest extends TestCase
{
    use Assertions;

    public function testAssignValueByBind()
    {
        // bind interface
        $container = new Container();
        $container->bind(AssignValueTestClassIF::class, AssignValueTestClass::class)->assign('dep', ['value' => "hello dependency!"]);

        $object = $container->get(AssignValueTestClass::class);
        static::assertInstanceOf(AssignValueTestClass::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        $object = $container->get(AssignValueTestClassIF::class);
        static::assertInstanceOf(AssignValueTestClass::class, $object);
        static::assertSame("hello dependency!", $object->getDep());

        // bind class directly
        $container = new Container();
        $container->bind(AssignValueTestClass::class)->assign('dep', ['value' => "hello dependency!"]);

        $object = $container->get(AssignValueTestClass::class);
        static::assertInstanceOf(AssignValueTestClass::class, $object);
        static::assertSame("hello dependency!", $object->getDep());
    }

    public function testAssignValueByClosure()
    {
        $container = new Container();
        $container->bind(AssignValueTestClassIF::class, function ($dep) {
            return new AssignValueTestClass($dep . ' from closure');
        })->assign('dep', ['value' => "hello dependency!"]);

        $object = $container->get(AssignValueTestClassIF::class);
        static::assertInstanceOf(AssignValueTestClass::class, $object);
        static::assertSame("hello dependency! from closure", $object->getDep());
    }
}

interface AssignValueTestClassIF {}
class AssignValueTestClass implements AssignValueTestClassIF
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
