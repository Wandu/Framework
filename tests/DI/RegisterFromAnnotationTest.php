<?php
namespace Wandu\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use Wandu\DI\Annotations as DI;

class RegisterFromAnnotationTest extends TestCase
{
    public function testSingleton()
    {
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class);
        $container->registerFromAnnotation(RegisterFromAnnotationTestClass::class);

        /** @var \Wandu\DI\RegisterFromAnnotationTestClass $actual */
        $actual = $container->get('test_main');
        static::assertInstanceOf(RegisterFromAnnotationTestClass::class, $actual);
        static::assertEquals([
            11, 22, null, new RegisterFromAnnotationTestClassDependency, 55
        ], $actual->getParams());
        static::assertEquals([
            1111, new RegisterFromAnnotationTestClassDependency, null
        ], $actual->getProperties());

        static::assertSame($actual->getParams()[3], $actual->getProperties()[1]);
        static::assertSame($actual, $container->get('test_main')); // singleton
        static::assertSame($actual, $container->get('test_main'));
        static::assertSame($actual, $container->get('test_main'));
    }

    public function testFactoryDependency()
    {
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class);
        $container->registerFromAnnotation([
            RegisterFromAnnotationTestClassDependency::class,
            RegisterFromAnnotationTestClass::class,
        ]);

        /** @var \Wandu\DI\RegisterFromAnnotationTestClass $actual */
        $actual = $container->get('test_main');
        static::assertInstanceOf(RegisterFromAnnotationTestClass::class, $actual);
        static::assertEquals([
            11, 22, null, new RegisterFromAnnotationTestClassDependency, 55
        ], $actual->getParams());
        static::assertEquals([
            1111, new RegisterFromAnnotationTestClassDependency, null
        ], $actual->getProperties());

        static::assertEquals($actual->getParams()[3], $actual->getProperties()[1]);
        static::assertNotSame($actual->getParams()[3], $actual->getProperties()[1]);
        static::assertSame($actual, $container->get('test_main')); // singleton
        static::assertSame($actual, $container->get('test_main'));
        static::assertSame($actual, $container->get('test_main'));
    }
}

/**
 * @DI\Alias("test_main")
 * @DI\Factory
 */
class RegisterFromAnnotationTestClassDependency {}

/**
 * @DI\Alias("test_main")
 */
class RegisterFromAnnotationTestClass
{
    private $param1;
    private $param2;
    private $param3;
    private $param4;
    private $param5;

    /** @DI\WireValue(1111) */
    private $property1;

    /**
     * @DI\Wire(RegisterFromAnnotationTestClassDependency::class)
     */
    private $property2;
    private $property3;

    /**
     * @DI\AssignValue(name="param1", value=11)
     * @DI\AssignValue(name="param2", value=22)
     * @DI\Assign(name="param4", target=RegisterFromAnnotationTestClassDependency::class)
     * @DI\AssignValue(name="param5", value=55)
     */
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
