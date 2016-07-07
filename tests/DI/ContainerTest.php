<?php
namespace Wandu\DI;

use Interop\Container\ContainerInterface as InteropContainer;
use Wandu\DI\Containee\ClosureContainee;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Stub\HttpController;
use Wandu\DI\Stub\JsonRenderer;
use Wandu\DI\Stub\Renderable;
use Wandu\DI\Stub\ServerAccessible;
use Wandu\DI\Stub\XmlRenderer;
use PHPUnit_Framework_TestCase;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $container = new Container();

        $this->assertSame($container, $container->get('container'));
        $this->assertSame($container, $container->get(Container::class));
        $this->assertSame($container, $container->get(ContainerInterface::class));
        $this->assertSame($container, $container->get(InteropContainer::class));
    }

    public function testHas()
    {
        $container = new Container();

        $container->instance(Renderable::class, new XmlRenderer);

        $this->assertTrue($container->has(Renderable::class)); // set by instance
        $this->assertFalse($container->has(ServerAccessible::class)); // interface false
        $this->assertTrue($container->has(JsonRenderer::class)); // class true

        // "has" map to offsetExists
        $this->assertTrue(isset($container[Renderable::class]));
        $this->assertFalse(isset($container[ServerAccessible::class]));
        $this->assertTrue(isset($container[JsonRenderer::class]));
    }

    public function testHasNull()
    {
        $container = new Container();

        $container->instance('null', null);

        $this->assertTrue($container->has('null'));

        // "has" map to offsetExists but except null.
        $this->assertFalse(isset($container['null']));
    }

    public function testSet()
    {
        $container = new Container();
        $xml = new XmlRenderer();
        $json = new JsonRenderer();
        
        $container->set('xml', $xml);

        // "set" map to offsetSet
        $container['json'] = $json;

        $this->assertSame($xml, $container->get('xml'));
        $this->assertSame($json, $container->get('json'));

        // "get" map to offsetGet
        $this->assertSame($xml, $container['xml']);
        $this->assertSame($json, $container['json']);
    }

    public function testSetWithContainee()
    {
        $container = new Container();

        $container->set('xml', new ClosureContainee(function () {
            return new XmlREnderer();
        }));

        // "set" map to offsetSet
        $container['json'] = new ClosureContainee(function () {
            return new JsonRenderer();
        });

        $this->assertInstanceOf(XmlRenderer::class, $container->get('xml'));
        $this->assertInstanceOf(JsonRenderer::class, $container->get('json'));
    }

    public function testInstance()
    {
        $container = new Container();
        $xml = new XmlRenderer();
        
        $container->instance('xml', $xml);

        $this->assertSame($xml, $container->get('xml'));

        // "get" map to offsetGet
        $this->assertSame($xml, $container['xml']);
    }

    public function testClosure()
    {
        $container = new Container();

        $container->instance(Renderable::class, $renderer = new XmlRenderer());
        $container->closure(HttpController::class, function ($app) {
            return new HttpController($app[Renderable::class], [
                'username' => 'username string',
                'password' => 'password string',
            ]);
        });

        $this->assertInstanceOf(HttpController::class, $container[HttpController::class]);
        $this->assertSame($container[HttpController::class], $container[HttpController::class]);
        $this->assertSame($renderer, $container[HttpController::class]->getRenderer());
        $this->assertEquals([
            'username' => 'username string',
            'password' => 'password string',
        ], $container[HttpController::class]->getConfig());
    }

    public function testAlias()
    {
        $container = new Container();
        $renderer = new XmlRenderer;

        $container->instance(Renderable::class, $renderer);

        $container->alias('myalias', Renderable::class);
        $container->alias('otheralias', 'myalias');

        $this->assertSame($renderer, $container[Renderable::class]);
        $this->assertSame($renderer, $container['myalias']);
        $this->assertSame($renderer, $container['otheralias']);
    }

    public function testGetByCreate()
    {
        $container = new Container();

        $controller = $container->get(JsonRenderer::class);
        $this->assertInstanceOf(JsonRenderer::class, $controller);
    }

    public function testGetFail()
    {
        $container = new Container();

        try {
            $container->get('unknown');
            $this->fail();
        } catch (NullReferenceException $exception) {
            $this->assertEquals('unknown', $exception->getClass());
        }
    }

    public function testDestroy()
    {
        $container = new Container();

        $container->instance('xml', new XmlRenderer());

        $this->assertTrue($container->has('xml'));

        $container->destroy('xml');

        $this->assertFalse($container->has('xml'));
    }

    public function testDestroyMany()
    {
        $container = new Container();

        $container->instance('xml1', new XmlRenderer());
        $container->instance('xml2', new XmlRenderer());

        $this->assertTrue($container->has('xml1'));
        $this->assertTrue($container->has('xml2'));

        $container->destroy('xml1', 'xml2');

        $this->assertFalse($container->has('xml1'));
        $this->assertFalse($container->has('xml2'));
    }

    public function testFrozen()
    {
        $container = new Container();

        $container->instance('instance', 'instance string');
        $container->closure('closure', function () {
            return 'closure string';
        });
        $container->alias('alias', 'closure');

        // all change
        $container->instance('instance', 'instance string changed');
        $container->closure('closure', function () {
            return 'closure string changed';
        });
        $container->alias('alias', 'instance');

        // call, then it freeze all values.
        $container->get('instance');
        $container->get('closure');
        $container->get('alias');

        // now cannot change
        try {
            $container->instance('instance', 'instance string changed 2');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; instance', $exception->getMessage());
        }
        try {
            $container->closure('closure', function () {
                return 'closure string change 2';
            });
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; closure', $exception->getMessage());
        }
        try {
            $container->alias('alias', 'closure');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; alias', $exception->getMessage());
        }

        // also cannot remove
        try {
            $container->offsetUnset('instance');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; instance', $exception->getMessage());
        }
        try {
            $container->offsetUnset('closure');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; closure', $exception->getMessage());
        }
        try {
            $container->offsetUnset('alias');
            $this->fail();
        } catch (CannotChangeException $exception) {
            $this->assertEquals('It cannot be changed; alias', $exception->getMessage());
        }
    }
}
