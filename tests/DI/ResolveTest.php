<?php
namespace Wandu\DI;

use ArrayAccess;
use ArrayObject;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Stub\Resolve\AutoResolvedDepend;
use Wandu\DI\Stub\Resolve\CallExample;
use Wandu\DI\Stub\Resolve\CreateNormalExample;
use Wandu\DI\Stub\Resolve\CreateWithArrayExample;
use Wandu\DI\Stub\Resolve\DependInterface;
use Wandu\DI\Stub\Resolve\ReplacedDepend;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Psr\ServerRequest;

class ResolveTest extends TestCase
{
    public function testBind()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);

        $this->assertInstanceOf(
            AutoResolvedDepend::class,
            $instance1 = $this->container->get(AutoResolvedDepend::class)
        );

        $this->assertInstanceOf(
            AutoResolvedDepend::class,
            $instance2 = $this->container->get(DependInterface::class)
        );
        
        $this->assertSame($instance1, $instance2);
    }

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
            DependInterface::class => new ReplacedDepend(), // key => value mean use this
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
        $this->assertEquals([1, 2], $this->container->call($callback, [1, 2]));
        $this->assertEquals([1, 2], $this->container->call($callback, [1, 2, 'foo' => 'foo string']));

        $callback = function ($foo = null) {
            return func_get_args();
        };

        $this->assertEquals([null], $this->container->call($callback));
        $this->assertEquals([1, 2], $this->container->call($callback, [1, 2]));
        $this->assertEquals(
            [1, 2],
            $this->container->call($callback, ['foo' => 'foo!', 1, 2,])
        );
        $this->assertEquals(
            [1, 2],
            $this->container->call($callback, [1, 2, 'foo' => 'foo!'])
        );

        $callback = function ($foo = 'default', $bar = null) {
            return func_get_args();
        };

        $this->assertEquals(['default', null], $this->container->call($callback));
        $this->assertEquals([null, 2], $this->container->call($callback, [null, 2]));
        $this->assertEquals([1, 2], $this->container->call($callback, [1, 2]));
        $this->assertEquals(
            [1, 2],
            $this->container->call($callback, [1, 2, 'foo' => 'foo!'])
        );

        $callback = function (ArrayAccess $foo = null) {
            return func_get_args();
        };

        $this->assertEquals([null], $this->container->call($callback));
        $this->assertEquals([null, 1, 2], $this->container->call($callback, [1, 2]));

        // instantly insert!
        $param = new ArrayObject();
        $this->assertEquals(
            [null, 1, 2, 3],
            $this->container->call($callback, [1, 2, 3, 'foo' => $param])
        );
        $this->assertEquals(
            [$param, 1, 2, 3],
            $this->container->call($callback, [1, 2, 3, ArrayAccess::class => $param])
        );

        $this->assertFalse($this->container->has(ArrayAccess::class));
    }
    
    public function testResolveException()
    {
        try {
            $this->container->get(StubResolveException1Depth::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals('unknown', $e->getParameter());
            $this->assertEquals(StubResolveException1Depth::class, $e->getClass());
        }

        try {
            $this->container->get(StubResolveException2Depth::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals('unknown', $e->getParameter());
            $this->assertEquals(StubResolveException1Depth::class, $e->getClass());
        }
    }
    
    public function testCascadeResolve()
    {
        $count = 0;
        
        $request = new ServerRequest();
        $request = $request->withParsedBody(['abc' => 'def']);
        
        $this->container->call(function (ServerRequestInterface $req, ParsedBody $parsedBody) use (&$count, $request) {
            $this->assertSame($request, $req);
            $count++;
        }, [
            ServerRequestInterface::class => $request,
        ]);
        
        $this->assertEquals(1, $count);
    }
}

class StubResolveException1Depth
{
    public function __construct(UnknownDepend $unknown)
    {
    }
}

class StubResolveException2Depth
{
    public function __construct(StubResolveException1Depth $depth1)
    {
    }
}
