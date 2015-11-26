<?php
namespace Wandu\DI;

use ArrayObject;
use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Stub\Invoker;
use Wandu\DI\Stub\StubClient;
use Wandu\DI\Stub\StubClientWithConfig;
use Wandu\DI\Stub\DepBar;
use Wandu\DI\Stub\DepFoo;
use Wandu\DI\Stub\DepInterface;
use Wandu\DI\Stub\NonDepInterface;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    /** @var ContainerInterface */
    protected $container;

    /** @var ArrayObject */
    protected $configs;

    public function setUp()
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testHas()
    {
        $this->container->instance(DepInterface::class, new DepFoo);

        $this->assertFalse($this->container->has(NonDepInterface::class));
        $this->assertTrue($this->container->has(DepInterface::class));

        // has map to offsetExists
        $this->assertFalse(isset($this->container[NonDepInterface::class]));
        $this->assertTrue(isset($this->container[DepInterface::class]));
    }

    public function testInstance()
    {
        $this->container->instance('foo', $foo = new DepFoo());

        // instance map to offsetSet
        $this->container['bar'] = $bar = new DepBar();

        $this->assertSame($foo, $this->container->get('foo'));
        $this->assertSame($bar, $this->container->get('bar'));

        // get map to offsetGet
        $this->assertSame($foo, $this->container['foo']);
        $this->assertSame($bar, $this->container['bar']);
    }

    public function testGetUnknown()
    {
        try {
            $this->container->get('Unknown');
            $this->fail();
        } catch (NullReferenceException $exception) {
            $this->assertEquals('not exists in this container; Unknown', $exception->getMessage());
        }
    }

    public function testClosure()
    {
        $this->container->closure(DepInterface::class, function () {
            return new DepFoo;
        });

        $this->assertEquals($this->container[DepInterface::class], $this->container[DepInterface::class]);
        $this->assertSame($this->container[DepInterface::class], $this->container[DepInterface::class]);
    }

    public function testClosureParameters()
    {
        $this->container[DepInterface::class] = $foo = new DepFoo();
        $this->container->closure(StubClientWithConfig::class, function ($app) {
            return new StubClientWithConfig($app[DepInterface::class], [
                'username' => 'username string',
                'password' => 'password string',
            ]);
        });

        $this->assertSame($foo, $this->container[StubClientWithConfig::class]->getDependency());
        $this->assertEquals([
            'username' => 'username string',
            'password' => 'password string',
        ], $this->container[StubClientWithConfig::class]->getConfig());
    }

    public function testAlias()
    {
        $this->container->instance(DepInterface::class, $foo = new DepFoo);

        $this->container->alias('myalias', DepInterface::class);
        $this->container->alias('otheralias', 'myalias');

        $this->assertSame($foo, $this->container[DepInterface::class]);
        $this->assertSame($foo, $this->container['myalias']);
        $this->assertSame($foo, $this->container['otheralias']);
    }

    public function testExtend()
    {
        $this->container->instance('instance', new DepFoo());
        $this->container->closure('closure', function () {
            return new DepBar();
        });
        $this->container->extend('instance', function ($item) {
            $item->contents = 'instance contents';
            return $item;
        });
        $this->container->extend('closure', function ($item) {
            $item->contents = 'closure contents';
            return $item;
        });

        $this->assertEquals('instance contents', $this->container['instance']->contents);
        $this->assertEquals('closure contents', $this->container['closure']->contents);

        $this->assertSame($this->container['closure'], $this->container['closure']);
        $this->assertSame($this->container['instance'], $this->container['instance']);

        try {
            $this->container->extend('Unknown', function ($item) {
                return $item .' extended..';
            });
            $this->fail();
        } catch (NullReferenceException $e) {
            $this->assertEquals('not exists in this container; Unknown', $e->getMessage());
        }
    }

    public function testAliasExtend()
    {
        $this->container = new Container(new ArrayObject());
        $this->container->instance('instance1', new DepFoo);
        $this->container->instance('instance2', new DepBar);

        $this->container->alias('myalias', 'instance1');
        $this->container->alias('otheralias', 'myalias');

        $this->container->extend('otheralias', function ($item) {
            $item->contents = 'alias contents';
            return $item;
        });

        $this->assertEquals('alias contents', $this->container['instance1']->contents);
    }

    public function testFrozon()
    {
        $this->container->instance('instance', 'instance string');
        $this->container->closure('closure', function () {
            return 'closure string';
        });
        $this->container->alias('alias', 'closure');

        // all change
        $this->container->instance('instance', 'instance string changed');
        $this->container->closure('closure', function () {
            return 'closure string changed';
        });
        $this->container->alias('alias', 'instance');

        // call, that frozen all values.
        $this->container->get('instance');
        $this->container->get('closure');
        $this->container->get('alias');

        // now cannot change
        try {
            $this->container->instance('instance', 'instance string changed 2');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; instance', $exception->getMessage());
        }
        try {
            $this->container->closure('closure', function () {
                return 'closure string change 2';
            });
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; closure', $exception->getMessage());
        }
        try {
            $this->container->alias('alias', 'closure');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; alias', $exception->getMessage());
        }

        // also cannot remove
        try {
            $this->container->offsetUnset('instance');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; instance', $exception->getMessage());
        }
        try {
            $this->container->offsetUnset('closure');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; closure', $exception->getMessage());
        }
        try {
            $this->container->offsetUnset('alias');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; alias', $exception->getMessage());
        }
    }

    public function testRegisterServiceProvider()
    {
        $mockProvider = Mockery::mock(ServiceProviderInterface::class);
        $mockProvider->shouldReceive('register')->with($this->container);

        $this->assertSame($this->container, $this->container->register($mockProvider));
    }

    public function testResolve()
    {
        $this->container->closure(DepInterface::class, function () {
            return new DepFoo();
        });

        $this->assertInstanceOf(StubClient::class, $this->container->create(StubClient::class));
        try {
            $this->container->create(StubClientWithConfig::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals('Auto resolver can resolve the class that use params with type hint; Wandu\DI\Stub\StubClientWithConfig', $e->getMessage());
        }
    }

    public function testResolveWithArguments()
    {
        $this->container->closure(DepInterface::class, function () {
            return new DepFoo();
        });

        $created = $created = $this->container->create(StubClientWithConfig::class, ['config' => 'config string!']);

        $this->assertInstanceOf(StubClientWithConfig::class, $created);
        $this->assertEquals(['config' => 'config string!'], $created->getConfig());
    }

    /**
     * test 6 types of callable
     */
    public function testCall()
    {
        $this->container->closure(DepInterface::class, function () {
            return new DepFoo();
        });

        function stub(DepInterface $dep)
        {
            return 'call function';
        }

        // closure
        $this->assertEquals('call closure', $this->container->call(function (DepInterface $dep) {
            return 'call closure';
        }));

        // function
        $this->assertEquals('call function', $this->container->call(__NAMESPACE__ . '\\stub'));

        // static method
        $this->assertInstanceOf(StubClient::class, $this->container->call(StubClient::class . '::create'));

        // array of static
        $this->assertInstanceOf(StubClient::class, $this->container->call([StubClient::class, 'create']));

        // array of method
        $this->assertEquals(
            'call with dependency',
            $this->container->call([new StubClient(new DepFoo), 'callWithDependency'])
        );

        // invoker
        $this->assertEquals(
            'invoke with',
            $this->container->call(new Invoker())
        );

    }

    public function testAutoResolveBind()
    {
        $this->container->bind(DepInterface::class, DepFoo::class);
        $this->container->bind(StubClient::class);

        $this->assertInstanceOf(StubClient::class, $this->container->get(StubClient::class));

        $this->assertSame($this->container->get(StubClient::class), $this->container->get(StubClient::class));
    }

    public function testBindClassNameAlsoAllow()
    {
        $this->container->bind(DepInterface::class, DepFoo::class);

        $this->assertInstanceOf(DepInterface::class, $this->container->get(DepInterface::class));
        $this->assertInstanceOf(DepInterface::class, $this->container->get(DepFoo::class));

        $this->assertSame($this->container->get(DepInterface::class), $this->container->get(DepFoo::class));
    }
}
