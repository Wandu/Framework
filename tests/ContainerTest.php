<?php
namespace Wandu\DI;

use ArrayObject;
use Mockery;
use PHPUnit_Framework_TestCase;
use stdClass;
use Wandu\DI\Stub\DepBar;
use Wandu\DI\Stub\DepFoo;
use Wandu\DI\Stub\DepInterface;
use Wandu\DI\Stub\NonDepInterface;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    /** @var ContainerInterface */
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $configs = new ArrayObject([
            'database' => []
        ]);
        $this->container = new Container($configs);
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
            $this->assertEquals('You cannot access null reference container; Unknown', $exception->getMessage());
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
        $this->container = new Container();
        $this->container->instance('instance', new stdClass());
        $this->container->singleton('singleton', function () {
            return new stdClass();
        });
        $this->container->extend('instance', function ($item) {
            $item->contents = 'added1!!';
            return $item;
        });
        $this->container->extend('singleton', function ($item) {
            $item->contents = 'added2!!';
            return $item;
        });

        $this->assertEquals('added1!!', $this->container['instance']->contents);
        $this->assertEquals('added2!!', $this->container['singleton']->contents);

        $this->assertEquals($this->container['singleton'], $this->container['singleton']);
        $this->assertSame($this->container['singleton'], $this->container['singleton']);

        $this->assertEquals($this->container['instance'], $this->container['instance']);
        $this->assertSame($this->container['instance'], $this->container['instance']);

        try {
            $this->container->extend('unknown', function ($item) {
                return $item .' extended..';
            });
            $this->fail();
        } catch (NullReferenceException $e) {
            $this->assertEquals('You cannot access null reference container; unknown', $e->getMessage());
        }
    }

    public function testAliasExtend()
    {
        $this->container = new Container();
        $this->container->instance('instance1', 'Test String 1!');
        $this->container->instance('instance2', 'Test String 2!');

        $this->container->alias('myalias', 'instance1');
        $this->container->alias('otheralias', 'myalias');

        $this->container->extend('otheralias', function ($item) {
            return $item .' extended..';
        });

        $this->assertEquals('Test String 1! extended..', $this->container['instance1']);
    }


    public function testDependency()
    {
        $this->container = new Container();
        $this->container['mammal'] = 'mammal!';
        $this->container->singleton('person', function ($c) {
            return 'person is ' .$c['mammal'];
        });

        $this->assertEquals('person is mammal!', $this->container['person']);
    }

    /**
     * ref. pimple
     */
    public function testFrozon()
    {
        $this->container = new Container();
        $this->container->instance('instance', 'instance text');
        $this->container->singleton('singleton', function () {
            return 'singleton text';
        });
        $this->container->alias('alias', 'singleton');

        $this->assertEquals('instance text', $this->container['instance']);
        $this->assertEquals('singleton text', $this->container['singleton']);
        $this->assertEquals('singleton text', $this->container['alias']);

        try {
            $this->container->instance('instance', 'instance text change');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; instance', $exception->getMessage());
        }
        try {
            $this->container->singleton('singleton', function () {
                return 'singleton text change';
            });
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; singleton', $exception->getMessage());
        }
        try {
            $this->container->alias('alias', 'factory');
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; alias', $exception->getMessage());
        }
    }

    public function testRegister()
    {
        $this->container = new Container();

        $mockProvider = Mockery::mock(ServiceProviderInterface::class);
        $mockProvider->shouldReceive('register')->with($this->container);

        $this->assertSame($this->container, $this->container->register($mockProvider));
    }


    public function testAutoResolveConstructor()
    {
        $resolver = new AutoResolver();

        $resolver->bind(StubAutoNeededInterface::class, StubAutoNeeded::class);
        $resolver->bind(StubAuto::class);

        $this->assertInstanceOf(StubAuto::class, $resolver->resolve(StubAuto::class));

        $this->assertEquals($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));
        $this->assertNotSame($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));
    }

    public function testAutoResolveWithMethod()
    {
        $resolver = new AutoResolver();

        $resolver->bind(StubAutoNeededInterface::class, StubAutoNeeded::class);
        $resolver->bind(StubAuto::class);

        $this->assertInstanceOf(StubAuto::class, $resolver->resolve(StubAuto::class));

        $this->assertEquals($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));
        $this->assertNotSame($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));

    }
}


