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
use PHPUnit_Framework_TestCase;

class ResolveTest extends PHPUnit_Framework_TestCase
{
    public function testBind()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        $this->assertInstanceOf(
            AutoResolvedDepend::class,
            $instance1 = $container->get(AutoResolvedDepend::class)
        );

        $this->assertInstanceOf(
            AutoResolvedDepend::class,
            $instance2 = $container->get(DependInterface::class)
        );
        
        $this->assertSame($instance1, $instance2);
    }

    public function testCreateFail()
    {
        $container = new Container();

        try {
            $container->create(CreateNormalExample::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals(CreateNormalExample::class, $e->getClass());
        }
    }
    
    public function testCreateSuccess()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        $this->assertInstanceOf(
            CreateNormalExample::class,
            $container->create(CreateNormalExample::class)
        );

        $this->assertInstanceOf(
            AutoResolvedDepend::class,
            $container->create(CreateNormalExample::class)->getDepend()
        );
    }

    public function testCreateFailBecauseOfTypeHint()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        try {
            $container->create(CreateWithArrayExample::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals(CreateWithArrayExample::class, $e->getClass());
        }
    }

    public function testCreateWithArguments()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        $created = $container->create(CreateWithArrayExample::class, [
            ['config' => 'config string!'],
        ]);

        $this->assertInstanceOf(CreateWithArrayExample::class, $created);

        $this->assertEquals(['config' => 'config string!'], $created->getConfigs());
        $this->assertInstanceOf(AutoResolvedDepend::class, $created->getDepend());
    }

    public function testCreateWithOtherDepend()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        $created = $container->create(CreateWithArrayExample::class, [
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
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        function stub(DependInterface $dep)
        {
            return 'call function';
        }

        // closure
        $this->assertEquals('call closure', $container->call(function (DependInterface $dep) {
            return 'call closure';
        }));

        // function
        $this->assertEquals('call function', $container->call(__NAMESPACE__ . '\\stub'));

        // static method
        $this->assertEquals(
            'static method',
            $container->call(CallExample::class . '::staticMethod')
        );

        // array of static
        $this->assertEquals(
            'static method',
            $container->call([CallExample::class, 'staticMethod'])
        );

        // array of method
        $this->assertEquals(
            'instance method',
            $container->call([new CallExample, 'instanceMethod'])
        );

        // invoker
        $this->assertEquals(
            'invoke',
            $container->call(new CallExample())
        );
    }

    public function testCallWithParams()
    {
        $container = new Container();

        $callback = function () {
            return func_get_args();
        };

        $this->assertEquals([], $container->call($callback));
        $this->assertEquals([1, 2], $container->call($callback, [1, 2]));
        $this->assertEquals([1, 2], $container->call($callback, [1, 2, 'foo' => 'foo string']));

        $callback = function ($foo = null) {
            return func_get_args();
        };

        $this->assertEquals([null], $container->call($callback));
        $this->assertEquals([1, 2], $container->call($callback, [1, 2]));
        $this->assertEquals(
            [1, 2],
            $container->call($callback, ['foo' => 'foo!', 1, 2,])
        );
        $this->assertEquals(
            [1, 2],
            $container->call($callback, [1, 2, 'foo' => 'foo!'])
        );

        $callback = function ($foo = 'default', $bar = null) {
            return func_get_args();
        };

        $this->assertEquals(['default', null], $container->call($callback));
        $this->assertEquals([null, 2], $container->call($callback, [null, 2]));
        $this->assertEquals([1, 2], $container->call($callback, [1, 2]));
        $this->assertEquals(
            [1, 2],
            $container->call($callback, [1, 2, 'foo' => 'foo!'])
        );

        $callback = function (ArrayAccess $foo = null) {
            return func_get_args();
        };

        $this->assertEquals([null], $container->call($callback));
        $this->assertEquals([null, 1, 2], $container->call($callback, [1, 2]));

        // instantly insert!
        $param = new ArrayObject();
        $this->assertEquals(
            [null, 1, 2, 3],
            $container->call($callback, [1, 2, 3, 'foo' => $param])
        );
        $this->assertEquals(
            [$param, 1, 2, 3],
            $container->call($callback, [1, 2, 3, ArrayAccess::class => $param])
        );

        $this->assertFalse($container->has(ArrayAccess::class));
    }
    
    public function testResolveException()
    {
        $container = new Container();

        try {
            $container->get(StubResolveException1Depth::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals('unknown', $e->getParameter());
            $this->assertEquals(StubResolveException1Depth::class, $e->getClass());
        }

        try {
            $container->get(StubResolveException2Depth::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals('unknown', $e->getParameter());
            $this->assertEquals(StubResolveException1Depth::class, $e->getClass());
        }
    }
    
    public function testCascadeResolve()
    {
        $container = new Container();

        $count = 0;
        
        $request = new ServerRequest();
        $request = $request->withParsedBody(['abc' => 'def']);
        
        $container->call(function (ServerRequestInterface $req, ParsedBody $parsedBody) use (&$count, $request) {
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
