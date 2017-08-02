<?php
namespace Wandu\DI\Descriptor;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;

class WireValueTest extends TestCase
{
    use Assertions;

    public function testWireValueWithBind()
    {
        $container = new Container();
        $container->bind('class', WireValueTestClass::class)
            ->wire('property', ['value' => 'inject property value!']);

        static::assertInstanceOf(WireValueTestClass::class, $container['class']);
        static::assertEquals('inject property value!', $container['class']->getProperty());
    }

    public function testWireValueWithInstance()
    {
        $container = new Container();
        $container->instance('instance', $renderer = new WireValueTestClass())
            ->wire('property', ['value' => 'inject property value!']);

        static::assertNull($container['instance']->getProperty());
        static::assertSame($renderer, $container['instance']);
    }

    public function testWireValueWithClosure()
    {
        $container = new Container();

        $container->bind('closure', function () {
            return new WireValueTestClass();
        })->wire('property', ['value' => 'inject property value!']);

        static::assertInstanceOf(WireValueTestClass::class, $container['closure']);
        static::assertEquals('inject property value!', $container['closure']->getProperty());
    }
}

class WireValueTestClass {
    private $property;
    public function getProperty()
    {
        return $this->property;
    }
}
