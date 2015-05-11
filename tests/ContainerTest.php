<?php
namespace Wandu\DI;

use Mockery;
use PHPUnit_Framework_TestCase;
use stdClass;
use Wandu\Standard\DI\ServiceProviderInterface;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testOffsetExists()
    {
        $container = new Container();
        $container->instance('exist', 'value!');

        $this->assertFalse(isset($container['unknown']));
        $this->assertTrue(isset($container['exist']));
    }

    public function testOffsetSet()
    {
        $container = new Container();
        $container['foo'] = ['number' => 1, 'string' => 'text!!'];
        $container->instance('bar', ['number' => 2, 'string' => 'text??']);

        $this->assertEquals(['number' => 1, 'string' => 'text!!'], $container['foo']);
        $this->assertEquals(['number' => 2, 'string' => 'text??'], $container['bar']);
    }

    public function testUnknownCall()
    {
        $container = new Container();
        try {
            $container['none'];
            $this->fail();
        } catch (NullReferenceException $exception) {
            $this->assertEquals('You cannot access null reference container.', $exception->getMessage());
        }
    }

    public function testFactory()
    {
        $container = new Container();
        $container->factory('factory', function () {
            return new stdClass();
        });

        $this->assertEquals($container['factory'], $container['factory']);
        $this->assertNotSame($container['factory'], $container['factory']);
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
        $container->factory('factory', function () {
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
        $container->extend('factory', function ($item) {
            $item->contents = 'added3!!';
            return $item;
        });

        $this->assertEquals('added1!!', $container['instance']->contents);
        $this->assertEquals('added2!!', $container['singleton']->contents);
        $this->assertEquals('added3!!', $container['factory']->contents);

        $this->assertEquals($container['factory'], $container['factory']);
        $this->assertNotSame($container['factory'], $container['factory']);

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
            $this->assertEquals('You cannot access null reference container.', $e->getMessage());
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
        $container->factory('factory', function () {
            return 'factory text';
        });
        $container->alias('alias', 'singleton');

        $this->assertEquals('instance text', $container['instance']);
        $this->assertEquals('singleton text', $container['singleton']);
        $this->assertEquals('factory text', $container['factory']);
        $this->assertEquals('singleton text', $container['alias']);

        try {
            $container->instance('instance', 'instance text change');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data.', $exception->getMessage());
        }
        try {
            $container->singleton('singleton', function () {
                return 'singleton text change';
            });
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data.', $exception->getMessage());
        }
        try {
            $container->factory('factory', function () {
                return 'factory text change';
            });
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data.', $exception->getMessage());
        }
        try {
            $container->alias('alias', 'factory');
        } catch (CannotChangeException $exception) {
            $this->assertEquals('You cannot change the data.', $exception->getMessage());
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
