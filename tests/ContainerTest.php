<?php
namespace Wandu\DI;

use Mockery;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Stub\HttpControllerWithConfig;
use Wandu\DI\Stub\JsonRenderer;
use Wandu\DI\Stub\Renderable;
use Wandu\DI\Stub\ServerAccessible;
use Wandu\DI\Stub\XmlRenderer;

class ContainerTest extends TestCase
{
    public function testHas()
    {
        $this->container->instance(Renderable::class, new XmlRenderer);

        $this->assertFalse($this->container->has(ServerAccessible::class));
        $this->assertTrue($this->container->has(Renderable::class));

        // has map to offsetExists
        $this->assertFalse(isset($this->container[ServerAccessible::class]));
        $this->assertTrue(isset($this->container[Renderable::class]));
    }

    public function testInstance()
    {
        $this->container->instance('xml', $xml = new XmlRenderer());

        // instance map to offsetSet
        $this->container['json'] = $json = new JsonRenderer();

        $this->assertSame($xml, $this->container->get('xml'));
        $this->assertSame($json, $this->container->get('json'));

        // get map to offsetGet
        $this->assertSame($xml, $this->container['xml']);
        $this->assertSame($json, $this->container['json']);
    }

    public function testClosure()
    {
        $this->container->closure(Renderable::class, function () {
            return new XmlRenderer;
        });

        $this->assertEquals($this->container[Renderable::class], $this->container[Renderable::class]);
        $this->assertSame($this->container[Renderable::class], $this->container[Renderable::class]);
    }

    public function testClosureWithParameters()
    {
        $this->container->instance(Renderable::class, $foo = new XmlRenderer());

        $this->container->closure(HttpControllerWithConfig::class, function ($app) {
            return new HttpControllerWithConfig(
                $app[Renderable::class],
                [
                    'username' => 'username string',
                    'password' => 'password string',
                ]
            );
        });

        $this->assertSame($foo, $this->container[HttpControllerWithConfig::class]->getRenderer());
        $this->assertEquals([
            'username' => 'username string',
            'password' => 'password string',
        ], $this->container[HttpControllerWithConfig::class]->getConfig());
    }

    public function testAlias()
    {
        $this->container->instance(Renderable::class, $foo = new XmlRenderer);

        $this->container->alias('myalias', Renderable::class);
        $this->container->alias('otheralias', 'myalias');

        $this->assertSame($foo, $this->container[Renderable::class]);
        $this->assertSame($foo, $this->container['myalias']);
        $this->assertSame($foo, $this->container['otheralias']);
    }

    public function testGetFail()
    {
        try {
            $this->container->get('unknown');
            $this->fail();
        } catch (NullReferenceException $exception) {
            $this->assertEquals('unknown', $exception->getClass());
        }
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
            $this->assertEquals('It cannot be changed; instance', $exception->getMessage());
        }
        try {
            $this->container->closure('closure', function () {
                return 'closure string change 2';
            });
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; closure', $exception->getMessage());
        }
        try {
            $this->container->alias('alias', 'closure');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; alias', $exception->getMessage());
        }

        // also cannot remove
        try {
            $this->container->offsetUnset('instance');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; instance', $exception->getMessage());
        }
        try {
            $this->container->offsetUnset('closure');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; closure', $exception->getMessage());
        }
        try {
            $this->container->offsetUnset('alias');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; alias', $exception->getMessage());
        }
    }
}
