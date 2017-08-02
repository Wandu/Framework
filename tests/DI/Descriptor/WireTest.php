<?php
namespace Wandu\DI\Descriptor;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;

class WireTest extends TestCase
{
    use Assertions;

    public function testWireWithInstance()
    {
        // instance cannot use
        $container = new Container();
        $container->instance('instance', new WireTestClass)->wire('property', WireTestClassDependency::class);

        static::assertNull($container['instance']->getProperty());
    }

    public function testWireWithBind()
    {
        $container = new Container();
        $container->bind('class', WireTestClass::class)->wire('property', WireTestClassDependency::class);

        static::assertInstanceOf(WireTestClass::class, $container['class']);
        static::assertInstanceOf(WireTestClassDependency::class, $container['class']->getProperty());

        $container = new Container();
        $container->bind('dependency', WireTestClassDependency::class);
        $container->bind('class', WireTestClass::class)->wire('property', 'dependency');

        static::assertInstanceOf(WireTestClass::class, $container['class']);
        static::assertInstanceOf(WireTestClassDependency::class, $container['class']->getProperty());
    }

    public function testWireWithClosure()
    {
        $container = new Container();
        $container->bind('closure', function () {
            return new WireTestClass();
        })->wire('property', WireTestClassDependency::class);

        static::assertInstanceOf(WireTestClass::class, $container['closure']);
        static::assertInstanceOf(WireTestClassDependency::class, $container['closure']->getProperty());
        
        $container = new Container();
        $container->bind('dependency', WireTestClassDependency::class);
        $container->bind('closure', function () {
            return new WireTestClass();
        })->wire('property', 'dependency');

        static::assertInstanceOf(WireTestClass::class, $container['closure']);
        static::assertInstanceOf(WireTestClassDependency::class, $container['closure']->getProperty());
    }
}

class WireTestClassDependency {}
class WireTestClass {
    private $property;
    public function getProperty()
    {
        return $this->property;
    }
}
