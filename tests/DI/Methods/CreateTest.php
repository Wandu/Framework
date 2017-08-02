<?php
namespace Wandu\DI\Methods;

use PHPUnit\Framework\TestCase;
use stdClass;
use Wandu\Assertions;
use Wandu\DI\Container;
use ReflectionClass;
use Wandu\DI\Exception\CannotResolveException;

class CreateTest extends TestCase
{
    use Assertions;

    public function testCreateAutoResolveFail()
    {
        $container = new Container();

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->create(CreateTestHasTypedParam::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('typedParam', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(CreateTestHasTypedParam::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );
    }
    
    public function testCreateAutoResolveSuccess()
    {
        $container = new Container();
        $container->bind(CreateTestDependencyInterface::class, CreateTestDependency::class);

        $object = $container->create(CreateTestHasTypedParam::class);
        static::assertInstanceOf(CreateTestHasTypedParam::class, $object);
        static::assertInstanceOf(CreateTestDependencyInterface::class, $object->typedParam);
        static::assertInstanceOf(CreateTestDependency::class, $object->typedParam);
    }

    public function testCreateUntypedParamClassSuccess()
    {
        $container = new Container();

        // by seq array
        $object = $container->create(CreateTestHasUntypedParam::class, [['username' => 'wan2land']]);
        static::assertSame(['username' => 'wan2land'], $object->untypedParam);

        // by param name
        $object = $container->create(CreateTestHasUntypedParam::class, ['untypedParam' => ['username' => 'wan3land']]);
        static::assertSame(['username' => 'wan3land'], $object->untypedParam);
    }

    public function testCreateUntypedParamClassFail()
    {
        $container = new Container();

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->create(CreateTestHasUntypedParam::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('untypedParam', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(CreateTestHasUntypedParam::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );
    }

    public function testCreateComplexParamsClassSuccess()
    {
        $container = new Container();

        // by assoc classname
        $object = $container->create(CreateTestHasTypedParam::class, [
            CreateTestDependencyInterface::class => $dep = new CreateTestDependency,
        ]);
        static::assertSame($dep, $object->typedParam);
        
        // by sequential
        $object = $container->create(CreateTestHasComplexParam::class, [
            $param1 = new CreateTestDependency(),
            $param2 = new stdClass,
        ]);
        static::assertSame($param1, $object->param1);
        static::assertSame($param2, $object->param2);
        static::assertSame('param3', $object->param3);
        static::assertSame('param4', $object->param4);

        // by assoc paramname
        $object = $container->create(CreateTestHasComplexParam::class, [
            'param1' => $param1 = new CreateTestDependency(),
            'param2' => $param2 = new stdClass,
            'param4' => $param4 = new stdClass,
        ]);
        static::assertSame($param1, $object->param1);
        static::assertSame($param2, $object->param2);
        static::assertSame('param3', $object->param3);
        static::assertSame($param4, $object->param4);
        
        // assoc with class name
        $object = $container->create(CreateTestHasComplexParam::class, [
            CreateTestDependencyInterface::class => $param1 = new CreateTestDependency(),
            'param2' => $param2 = new stdClass,
            'param4' => $param4 = new stdClass,
        ]);
        static::assertInstanceOf(CreateTestDependencyInterface::class, $object->param1);
        static::assertSame($param1, $object->param1);
        static::assertSame($param2, $object->param2);
        static::assertSame('param3', $object->param3);
        static::assertSame($param4, $object->param4);

        // complex
        $object = $container->create(CreateTestHasComplexParam::class, [
            $param1 = new CreateTestDependency(),
            $param2 = new stdClass,
            'param4' => $param4 = new stdClass,
            'param3' => $param3 = new stdClass,
        ]);
        static::assertSame($param1, $object->param1);
        static::assertSame($param2, $object->param2);
        static::assertSame($param3, $object->param3);
        static::assertSame($param4, $object->param4);
    }

    public function testCreateComplexParamsClassFail()
    {
        $container = new Container();

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->create(CreateTestHasComplexParam::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('param1', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(CreateTestHasComplexParam::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );

        $container->bind(CreateTestDependencyInterface::class, CreateTestDependency::class);

        /** @var \Wandu\DI\Exception\CannotResolveException $exception */
        $exception = static::catchException(function () use ($container) {
            $container->create(CreateTestHasComplexParam::class);
        });

        static::assertInstanceOf(CannotResolveException::class, $exception);
        static::assertEquals('param2', $exception->getParameter());
        static::assertEquals(__FILE__, $exception->getFile());
        static::assertEquals(
            (new ReflectionClass(CreateTestHasComplexParam::class))->getConstructor()->getStartLine(),
            $exception->getLine()
        );
    }
}

interface CreateTestDependencyInterface {}
class CreateTestDependency implements CreateTestDependencyInterface {}

class CreateTestHasTypedParam
{
    public function __construct(CreateTestDependencyInterface $typedParam)
    {
        $this->typedParam = $typedParam;
    }
}

class CreateTestHasUntypedParam
{
    public function __construct($untypedParam)
    {
        $this->untypedParam = $untypedParam;
    }
}

class CreateTestHasComplexParam
{
    public function __construct(CreateTestDependencyInterface $param1, $param2, $param3 = 'param3', $param4 = 'param4')
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
        $this->param4 = $param4;
    }
}
