<?php
namespace Wandu\DI;

use ArrayAccess;
use ArrayObject;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Stub\Resolve\AutoResolvedDepend;
use Wandu\DI\Stub\Resolve\CreateNormalExample;
use Wandu\DI\Stub\Resolve\CreateWithArrayExample;
use Wandu\DI\Stub\Resolve\DependInterface;
use Wandu\DI\Stub\Resolve\ReplacedDepend;
use Wandu\Http\Parameters\ParsedBody;
use Wandu\Http\Psr\ServerRequest;

class ResolveTest extends PHPUnit_Framework_TestCase
{
    public function testBind()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        static::assertInstanceOf(
            AutoResolvedDepend::class,
            $instance1 = $container->get(AutoResolvedDepend::class)
        );

        static::assertInstanceOf(
            AutoResolvedDepend::class,
            $instance2 = $container->get(DependInterface::class)
        );
        
        static::assertSame($instance1, $instance2);
    }

    public function testCreateFail()
    {
        $container = new Container();

        try {
            $container->create(CreateNormalExample::class);
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(CreateNormalExample::class, $e->getClass());
        }
    }
    
    public function testCreateSuccess()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        static::assertInstanceOf(
            CreateNormalExample::class,
            $container->create(CreateNormalExample::class)
        );

        static::assertInstanceOf(
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
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals(CreateWithArrayExample::class, $e->getClass());
        }
    }

    public function testCreateWithArguments()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        $created = $container->create(CreateWithArrayExample::class, [
            ['config' => 'config string!'],
        ]);

        static::assertInstanceOf(CreateWithArrayExample::class, $created);

        static::assertEquals(['config' => 'config string!'], $created->getConfigs());
        static::assertInstanceOf(AutoResolvedDepend::class, $created->getDepend());
    }

    public function testCreateWithOtherDepend()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        $created = $container->create(CreateWithArrayExample::class, [
            ['config' => 'config string!'],
            DependInterface::class => new ReplacedDepend(), // key => value mean use this
        ]);

        static::assertInstanceOf(CreateWithArrayExample::class, $created);

        static::assertEquals(['config' => 'config string!'], $created->getConfigs());
        static::assertInstanceOf(ReplacedDepend::class, $created->getDepend());
    }

    /**
     * test 6 types of callable
     */
    public function testCall()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);

        // closure
        static::assertEquals('call closure', $container->call(function (DependInterface $dep) {
            return 'call closure';
        }));

        // function
        static::assertEquals('call function', $container->call(__NAMESPACE__ . '\\stubFunction'));

        // static method
        static::assertEquals(
            'static method',
            $container->call(TestCallExample::class . '::staticMethod')
        );

        // array of static
        static::assertEquals(
            'static method',
            $container->call([TestCallExample::class, 'staticMethod'])
        );

        // array of method
        static::assertEquals(
            'instance method',
            $container->call([new TestCallExample, 'instanceMethod'])
        );

        // invoker
        static::assertEquals(
            'invoke',
            $container->call(new TestCallExample())
        );
        
        // __call
        static::assertEquals(
            ['__call', 'callViaCallMagicMethod', []],
            $container->call([new TestCallExample(), 'callViaCallMagicMethod'])
        );
        
        // __staticCall
        static::assertEquals(
            ['__callStatic', 'callViaStaticCallMagicMethod', []],
            $container->call([TestCallExample::class, 'callViaStaticCallMagicMethod'])
        );
    }

    public function testCallWithParams()
    {
        $container = new Container();

        $callback = function () {
            return func_get_args();
        };

        static::assertEquals([], $container->call($callback));
        static::assertEquals([1, 2], $container->call($callback, [1, 2]));
        static::assertEquals([1, 2], $container->call($callback, [1, 2, 'foo' => 'foo string']));

        $callback = function ($foo = null) {
            return func_get_args();
        };

        static::assertEquals([null], $container->call($callback));
        static::assertEquals([1, 2], $container->call($callback, [1, 2]));
        static::assertEquals(
            [1, 2],
            $container->call($callback, ['foo' => 'foo!', 1, 2,])
        );
        static::assertEquals(
            [1, 2],
            $container->call($callback, [1, 2, 'foo' => 'foo!'])
        );

        $callback = function ($foo = 'default', $bar = null) {
            return func_get_args();
        };

        static::assertEquals(['default', null], $container->call($callback));
        static::assertEquals([null, 2], $container->call($callback, [null, 2]));
        static::assertEquals([1, 2], $container->call($callback, [1, 2]));
        static::assertEquals(
            [1, 2],
            $container->call($callback, [1, 2, 'foo' => 'foo!'])
        );

        $callback = function (ArrayAccess $foo = null) {
            return func_get_args();
        };

        static::assertEquals([null], $container->call($callback));
        static::assertEquals([null, 1, 2], $container->call($callback, [1, 2]));

        // instantly insert!
        $param = new ArrayObject();
        static::assertEquals(
            [null, 1, 2, 3],
            $container->call($callback, [1, 2, 3, 'foo' => $param])
        );
        static::assertEquals(
            [$param, 1, 2, 3],
            $container->call($callback, [1, 2, 3, ArrayAccess::class => $param])
        );

        static::assertFalse($container->has(ArrayAccess::class));
    }
    
    public function testResolveException()
    {
        $container = new Container();

        try {
            $container->get(StubResolveException1Depth::class);
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals('unknown', $e->getParameter());
            static::assertEquals(StubResolveException1Depth::class, $e->getClass());
        }

        try {
            $container->get(StubResolveException2Depth::class);
            static::fail();
        } catch (CannotResolveException $e) {
            static::assertEquals('unknown', $e->getParameter());
            static::assertEquals(StubResolveException1Depth::class, $e->getClass());
        }
    }
    
    public function testCascadeResolve()
    {
        $container = new Container();

        $count = 0;
        
        $request = new ServerRequest();
        $request = $request->withParsedBody(['abc' => 'def']);
        
        $container->call(function (ServerRequestInterface $req, ParsedBody $parsedBody) use (&$count, $request) {
            static::assertSame($request, $req);
            $count++;
        }, [
            ServerRequestInterface::class => $request,
        ]);
        
        static::assertEquals(1, $count);
    }
}

function stubFunction(DependInterface $dep)
{
    return 'call function';
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

class TestCallExample
{
    /**
     * @return string
     */
    public static function staticMethod()
    {
        return 'static method';
    }

    /**
     * @return string
     */
    public function instanceMethod()
    {
        return 'instance method';
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        return 'invoke';
    }

    /**
     * @param $name
     * @param $arguments
     * @return array
     */
    public function __call($name, $arguments)
    {
        return ['__call', $name, $arguments];
    }

    /**
     * @param $name
     * @param $arguments
     * @return array
     */
    public static function __callStatic($name, $arguments)
    {
        return ['__callStatic', $name, $arguments];
    }
}
