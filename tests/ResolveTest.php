<?php
namespace Wandu\DI;

use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Stub\Resolve\AutoResolvedDepend;
use Wandu\DI\Stub\Resolve\CallExample;
use Wandu\DI\Stub\Resolve\CreateNormalExample;
use Wandu\DI\Stub\Resolve\CreateWithArrayExample;
use Wandu\DI\Stub\Resolve\DependInterface;
use Wandu\DI\Stub\Resolve\ReplacedDepend;

class ResolveTest extends TestCase
{
    public function testCreateFail()
    {
        try {
            $this->container->create(CreateNormalExample::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals(CreateNormalExample::class, $e->getClass());
        }
    }

    public function testCreateSuccess()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);

        $this->assertInstanceOf(
            CreateNormalExample::class,
            $this->container->create(CreateNormalExample::class)
        );

        $this->assertInstanceOf(
            AutoResolvedDepend::class,
            $this->container->create(CreateNormalExample::class)->getDepend()
        );
    }

    public function testCreateFailBecauseOfTypeHint()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);

        try {
            $this->container->create(CreateWithArrayExample::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals(CreateWithArrayExample::class, $e->getClass());
        }
    }

    public function testCreateWithArguments()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);

        $created = $this->container->create(CreateWithArrayExample::class, [
            ['config' => 'config string!'],
        ]);

        $this->assertInstanceOf(CreateWithArrayExample::class, $created);

        $this->assertEquals(['config' => 'config string!'], $created->getConfigs());
        $this->assertInstanceOf(AutoResolvedDepend::class, $created->getDepend());
    }

    public function testCreateWithOtherDepend()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);

        $created = $this->container->create(CreateWithArrayExample::class, [
            ['config' => 'config string!'],
            'depend' => new ReplacedDepend(), // key => value mean use this
        ]);

        $this->assertInstanceOf(CreateWithArrayExample::class, $created);

        $this->assertEquals(['config' => 'config string!'], $created->getConfigs());
        $this->assertInstanceOf(ReplacedDepend::class, $created->getDepend());
    }

    /**
     * test 6 types of callable
     */
    public function testCall()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);

        function stub(DependInterface $dep)
        {
            return 'call function';
        }

        // closure
        $this->assertEquals('call closure', $this->container->call(function (DependInterface $dep) {
            return 'call closure';
        }));

        // function
        $this->assertEquals('call function', $this->container->call(__NAMESPACE__ . '\\stub'));

        // static method
        $this->assertEquals(
            'static method',
            $this->container->call(CallExample::class . '::staticMethod')
        );

        // array of static
        $this->assertEquals(
            'static method',
            $this->container->call([CallExample::class, 'staticMethod'])
        );

        // array of method
        $this->assertEquals(
            'instance method',
            $this->container->call([new CallExample, 'instanceMethod'])
        );

        // invoker
        $this->assertEquals(
            'invoke',
            $this->container->call(new CallExample())
        );
    }

    public function testCallWithParams()
    {

        $callback = function () {
            return func_get_args();
        };

        $this->assertEquals([], $this->container->call($callback));
        $this->assertEquals([1,2,3], $this->container->call($callback, [1, 2, 3]));
    }
}
