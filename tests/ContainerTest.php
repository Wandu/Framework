<?php
namespace Wandu\DI;

use ArrayObject;
use Mockery;
use PHPUnit_Framework_TestCase;
use stdClass;

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

    public function testInstance()
    {
        $this->container->instance('exist', 'value!');
        $this->assertFalse(isset($this->container['unknown']));
        $this->assertTrue(isset($this->container['exist']));
    }

    public function testOffsetSet()
    {
        $this->container['foo'] = ['number' => 1, 'string' => 'text!!'];
        $this->container->instance('bar', ['number' => 2, 'string' => 'text??']);

        $this->assertEquals(['number' => 1, 'string' => 'text!!'], $this->container['foo']);
        $this->assertEquals(['number' => 2, 'string' => 'text??'], $this->container['bar']);
    }

    public function testUnknownCall()
    {
        $container = new Container();
        try {
            $container['none'];
            $this->fail();
        } catch (NullReferenceException $exception) {
            $this->assertEquals('You cannot access null reference container; none', $exception->getMessage());
        }
    }

    public function testSingleton()
    {
        $container = new Container();
        $container->singleton('singleton', function () {
            return new stdClass();
        });

        $this->assertEquals($container['singleton'], $container['singleton']);
        $this->assertSame($container['singleton'], $container['singleton']);
    }

    public function testAlias()
    {
        $container = new Container();
        $container->instance('instance1', 'Test String 1!');
        $container->instance('instance2', 'Test String 2!');

        $container->alias('myalias', 'instance1');
        $container->alias('otheralias', 'myalias');

        $this->assertEquals('Test String 1!', $container['myalias']);
        $this->assertEquals('Test String 1!', $container['otheralias']);
    }

    public function testExtend()
    {
        $container = new Container();
        $container->instance('instance', new stdClass());
        $container->singleton('singleton', function () {
            return new stdClass();
        });
        $container->extend('instance', function ($item) {
            $item->contents = 'added1!!';
            return $item;
        });
        $container->extend('singleton', function ($item) {
            $item->contents = 'added2!!';
            return $item;
        });

        $this->assertEquals('added1!!', $container['instance']->contents);
        $this->assertEquals('added2!!', $container['singleton']->contents);

        $this->assertEquals($container['singleton'], $container['singleton']);
        $this->assertSame($container['singleton'], $container['singleton']);

        $this->assertEquals($container['instance'], $container['instance']);
        $this->assertSame($container['instance'], $container['instance']);

        try {
            $container->extend('unknown', function ($item) {
                return $item .' extended..';
            });
            $this->fail();
        } catch (NullReferenceException $e) {
            $this->assertEquals('You cannot access null reference container; unknown', $e->getMessage());
        }
    }

    public function testAliasExtend()
    {
        $container = new Container();
        $container->instance('instance1', 'Test String 1!');
        $container->instance('instance2', 'Test String 2!');

        $container->alias('myalias', 'instance1');
        $container->alias('otheralias', 'myalias');

        $container->extend('otheralias', function ($item) {
            return $item .' extended..';
        });

        $this->assertEquals('Test String 1! extended..', $container['instance1']);
    }


    public function testDependency()
    {
        $container = new Container();
        $container['mammal'] = 'mammal!';
        $container->singleton('person', function ($c) {
            return 'person is ' .$c['mammal'];
        });

        $this->assertEquals('person is mammal!', $container['person']);
    }

    /**
     * ref. pimple
     */
    public function testFrozon()
    {
        $container = new Container();
        $container->instance('instance', 'instance text');
        $container->singleton('singleton', function () {
            return 'singleton text';
        });
        $container->alias('alias', 'singleton');

        $this->assertEquals('instance text', $container['instance']);
        $this->assertEquals('singleton text', $container['singleton']);
        $this->assertEquals('singleton text', $container['alias']);

        try {
            $container->instance('instance', 'instance text change');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; instance', $exception->getMessage());
        }
        try {
            $container->singleton('singleton', function () {
                return 'singleton text change';
            });
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; singleton', $exception->getMessage());
        }
        try {
            $container->alias('alias', 'factory');
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data; alias', $exception->getMessage());
        }
    }

    public function testRegister()
    {
        $container = new Container();

        $mockProvider = Mockery::mock(ServiceProviderInterface::class);
        $mockProvider->shouldReceive('register')->with($container);

        $this->assertSame($container, $container->register($mockProvider));
    }
}
