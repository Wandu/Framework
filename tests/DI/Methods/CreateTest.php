<?php
namespace Wandu\DI\Methods;

use PHPUnit_Framework_TestCase;
use stdClass;
use Wandu\DI\Container;
use Wandu\DI\Exception\CannotResolveException;

class CreateTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $container = new Container();

        // create fail..
        try {
            $container->create(CreateTestHasTypeParam::class);
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(CreateTestHasTypeParam::class, $e->getClass());
            static::assertEquals('param', $e->getParameter());
        }

        $container->bind(CreateTestDependencyInterface::class, CreateTestDependency::class);

        // create success
        $object = $container->create(CreateTestHasTypeParam::class);
        static::assertInstanceOf(CreateTestHasTypeParam::class, $object);

        // get dependency
        static::assertInstanceOf(CreateTestDependencyInterface::class, $object->param);
        static::assertInstanceOf(CreateTestDependency::class, $object->param);
    }

    public function testCreateSingleParamClassWithArguments()
    {
        $container = new Container();

        try {
            $container->create(CreateTestHasParam::class);
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(CreateTestHasParam::class, $e->getClass());
            static::assertEquals('param', $e->getParameter());
        }

        // single param class
        $object = $container->create(CreateTestHasParam::class, [['username' => 'wan2land']]);
        static::assertSame(['username' => 'wan2land'], $object->param);

        $object = $container->create(CreateTestHasParam::class, ['param' => ['username' => 'wan3land']]);
        static::assertSame(['username' => 'wan3land'], $object->param);
    }

    public function testCreateMultiParamsClassWithArguments()
    {
        $container = new Container();

        try {
            $container->create(CreateTestHasMultiParam::class);
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(CreateTestHasMultiParam::class, $e->getClass());
            static::assertEquals('param1', $e->getParameter());
        }

        $container->bind(CreateTestDependencyInterface::class, CreateTestDependency::class);

        try {
            $container->create(CreateTestHasMultiParam::class);
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(CreateTestHasMultiParam::class, $e->getClass());
            static::assertEquals('param2', $e->getParameter());
        }

        // only sequential
        $object = $container->create(CreateTestHasMultiParam::class, [
            $param1 = new CreateTestDependency(),
            $param2 = new stdClass,
        ]);
        static::assertSame($param1, $object->param1);
        static::assertSame($param2, $object->param2);
        static::assertSame('param3', $object->param3);
        static::assertSame('param4', $object->param4);

        // only assoc
        $object = $container->create(CreateTestHasMultiParam::class, [
            'param2' => $param2 = new stdClass,
            'param4' => $param4 = new stdClass,
        ]);
        static::assertInstanceOf(CreateTestDependencyInterface::class, $object->param1);
        static::assertSame($param2, $object->param2);
        static::assertSame('param3', $object->param3);
        static::assertSame($param4, $object->param4);

        // complex
        $object = $container->create(CreateTestHasMultiParam::class, [
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
}

interface CreateTestDependencyInterface {}
class CreateTestDependency implements CreateTestDependencyInterface {}

class CreateTestHasTypeParam
{
    public function __construct(CreateTestDependencyInterface $param)
    {
        $this->param = $param;
    }
}

class CreateTestHasParam
{
    public function __construct($param)
    {
        $this->param = $param;
    }
}

class CreateTestHasMultiParam
{
    public function __construct(CreateTestDependencyInterface $param1, $param2, $param3 = 'param3', $param4 = 'param4')
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
        $this->param4 = $param4;
    }
}
