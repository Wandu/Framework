<?php
namespace Wandu\DI;

use PHPUnit\Framework\TestCase;

class RegisterFromArrayTest extends TestCase
{
    public function testSingleton()
    {
        $container = new Container();
        $container->registerFromArray([
            'test_dependency' => [
                'class' => RegisterFromArrayTestClassDependency::class,
            ],
            'test_main' => [
                'class' => RegisterFromArrayTestClass::class,
                'assigns' => [
                    ['value' => 11],
                    ['value' => 22],
                    'param4' => 'test_dependency',
                    'param5' => ['value' => 55],
                ],
                'wires' => [
                    'property1' => ['value' => 1111],
                    'property2' => 'test_dependency',
                ],
            ],
        ]);
        
        /** @var \Wandu\DI\RegisterFromArrayTestClass $actual */
        $actual = $container->get('test_main');
        static::assertInstanceOf(RegisterFromArrayTestClass::class, $actual);
        static::assertEquals([
            11, 22, null, new RegisterFromArrayTestClassDependency, 55
        ], $actual->getParams());
        static::assertEquals([
            1111, new RegisterFromArrayTestClassDependency, null
        ], $actual->getProperties());

        static::assertSame($actual->getParams()[3], $actual->getProperties()[1]);
        static::assertSame($actual, $container->get('test_main')); // singleton
        static::assertSame($actual, $container->get('test_main'));
        static::assertSame($actual, $container->get('test_main'));
    }

    public function testFactoryDependency()
    {
        $container = new Container();
        $container->registerFromArray([
            'test_dependency' => [
                'class' => RegisterFromArrayTestClassDependency::class,
                'factory' => true,
            ],
            'test_main' => [
                'class' => RegisterFromArrayTestClass::class,
                'assigns' => [
                    ['value' => 11],
                    ['value' => 22],
                    'param4' => 'test_dependency',
                    'param5' => ['value' => 55],
                ],
                'wires' => [
                    'property1' => ['value' => 1111],
                    'property2' => 'test_dependency',
                ],
            ],
        ]);

        /** @var \Wandu\DI\RegisterFromArrayTestClass $actual */
        $actual = $container->get('test_main');
        static::assertInstanceOf(RegisterFromArrayTestClass::class, $actual);
        static::assertEquals([
            11, 22, null, new RegisterFromArrayTestClassDependency, 55
        ], $actual->getParams());
        static::assertEquals([
            1111, new RegisterFromArrayTestClassDependency, null
        ], $actual->getProperties());

        static::assertEquals($actual->getParams()[3], $actual->getProperties()[1]);
        static::assertNotSame($actual->getParams()[3], $actual->getProperties()[1]);
        static::assertSame($actual, $container->get('test_main')); // singleton
        static::assertSame($actual, $container->get('test_main'));
        static::assertSame($actual, $container->get('test_main'));
    }

    public function testFactory()
    {
        $container = new Container();
        $container->registerFromArray([
            'test_dependency' => [
                'class' => RegisterFromArrayTestClassDependency::class,
            ],
            'test_main' => [
                'class' => RegisterFromArrayTestClass::class,
                'factory' => true,
                'assigns' => [
                    ['value' => 11],
                    ['value' => 22],
                    'param4' => 'test_dependency',
                    'param5' => ['value' => 55],
                ],
                'wires' => [
                    'property1' => ['value' => 1111],
                    'property2' => 'test_dependency',
                ],
            ],
        ]);

        /** @var \Wandu\DI\RegisterFromArrayTestClass $actual */
        $actual = $container->get('test_main');
        static::assertInstanceOf(RegisterFromArrayTestClass::class, $actual);
        static::assertEquals([
            11, 22, null, new RegisterFromArrayTestClassDependency, 55
        ], $actual->getParams());
        static::assertEquals([
            1111, new RegisterFromArrayTestClassDependency, null
        ], $actual->getProperties());

        // all singleton!
        static::assertSame($actual->getParams()[3], $actual->getProperties()[1]);
        static::assertSame($actual->getParams()[3], $container->get('test_main')->getProperties()[1]);
        static::assertSame($actual->getParams()[3], $container->get('test_main')->getParams()[3]);

        // not Same..!! factory
        static::assertEquals($actual, $container->get('test_main'));
        static::assertEquals($actual, $container->get('test_main'));
        static::assertEquals($actual, $container->get('test_main'));
        static::assertNotSame($actual, $container->get('test_main'));
        static::assertNotSame($actual, $container->get('test_main'));
        static::assertNotSame($actual, $container->get('test_main'));
    }
}
class RegisterFromArrayTestClassDependency {}
class RegisterFromArrayTestClass
{
    private $param1;
    private $param2;
    private $param3;
    private $param4;
    private $param5;
    private $property1;
    private $property2;
    private $property3;
    public function __construct($param1, $param2, $param3 = null, $param4 = null, $param5 = null)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
        $this->param4 = $param4;
        $this->param5 = $param5;
    }
    public function getParams()
    {
        return [
            $this->param1,
            $this->param2,
            $this->param3,
            $this->param4,
            $this->param5,
        ];
    }
    public function getProperties()
    {
        return [
            $this->property1,
            $this->property2,
            $this->property3,
        ];
    }
}
