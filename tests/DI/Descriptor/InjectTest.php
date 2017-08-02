<?php
namespace Wandu\DI\Descriptor;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;

class InjectTest extends TestCase
{
    use Assertions;

    public function testInjectWithBind()
    {
        $container = new Container();
        $container->bind('class', InjectTestClass::class)->inject('property', 'inject property value!');

        static::assertInstanceOf(InjectTestClass::class, $container['class']);
        static::assertEquals('inject property value!', $container['class']->getProperty());
    }

    public function testInjectWithInstance()
    {
        $container = new Container();
        $container->instance('instance', $renderer = new InjectTestClass())->inject('property', 'inject property value!');

        static::assertNull($container['instance']->getProperty());
        static::assertSame($renderer, $container['instance']);
    }

    public function testInjectWithClosure()
    {
        $container = new Container();

        $container->bind('closure', function () {
            return new InjectTestClass();
        })->inject('property', 'inject property value!');

        static::assertInstanceOf(InjectTestClass::class, $container['closure']);
        static::assertEquals('inject property value!', $container['closure']->getProperty());
    }
}

class InjectTestClass {
    private $property;
    public function getProperty()
    {
        return $this->property;
    }
}
