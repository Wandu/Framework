<?php
namespace Wandu\DI;

use ArrayObject;
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

        static::assertSame($container, $container->get('container'));
        static::assertSame($container, $container->get(Container::class));
        static::assertSame($container, $container->get(ContainerInterface::class));
        static::assertSame($container, $container->get(InteropContainer::class));
    }

    public function testHas()
    {
        $container = new Container();

        $container->instance(Renderable::class, new XmlRenderer);

        static::assertTrue($container->has(Renderable::class)); // set by instance
        static::assertFalse($container->has(ServerAccessible::class)); // interface false
        static::assertTrue($container->has(JsonRenderer::class)); // class true

        // "has" map to offsetExists
        static::assertTrue(isset($container[Renderable::class]));
        static::assertFalse(isset($container[ServerAccessible::class]));
        static::assertTrue(isset($container[JsonRenderer::class]));
    }

    public function testHasNull()
    {
        $container = new Container();

        $container->instance('null', null);

        static::assertTrue($container->has('null'));

        // "has" map to offsetExists but except null.
        static::assertFalse(isset($container['null']));
    }

    public function testSet()
    {
        $container = new Container();
        $xml = new XmlRenderer();
        $json = new JsonRenderer();
        
        $container->set('xml', $xml);

        // "set" map to offsetSet
        $container['json'] = $json;

        static::assertSame($xml, $container->get('xml'));
        static::assertSame($json, $container->get('json'));

        // "get" map to offsetGet
        static::assertSame($xml, $container['xml']);
        static::assertSame($json, $container['json']);
    }

    public function testSetWithContainee()
    {
        $container = new Container();

        $container->set('xml', new ClosureContainee(function () {
            return new XmlRenderer();
        }));

        // "set" map to offsetSet
        $container['json'] = new ClosureContainee(function () {
            return new JsonRenderer();
        });

        static::assertInstanceOf(XmlRenderer::class, $container->get('xml'));
        static::assertInstanceOf(JsonRenderer::class, $container->get('json'));
    }

    public function testInstance()
    {
        $container = new Container();
        $xml = new XmlRenderer();
        
        $container->instance('xml', $xml);

        static::assertSame($xml, $container->get('xml'));

        // "get" map to offsetGet
        static::assertSame($xml, $container['xml']);
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

        static::assertInstanceOf(HttpController::class, $container[HttpController::class]);
        static::assertSame($container[HttpController::class], $container[HttpController::class]);
        static::assertSame($renderer, $container[HttpController::class]->getRenderer());
        static::assertEquals([
            'username' => 'username string',
            'password' => 'password string',
        ], $container[HttpController::class]->getConfig());
    }

    public function testClosureWithTypeHint()
    {
        $container = new Container();

        $container->instance(Renderable::class, $renderer = new XmlRenderer());
        $container->closure(HttpController::class, function (Renderable $renderable) {
            return new HttpController($renderable, [
                'username' => 'username string',
                'password' => 'password string',
            ]);
        });

        static::assertInstanceOf(HttpController::class, $container[HttpController::class]);
        static::assertSame($container[HttpController::class], $container[HttpController::class]);
        static::assertSame($renderer, $container[HttpController::class]->getRenderer());
        static::assertEquals([
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

        static::assertSame($renderer, $container[Renderable::class]);
        static::assertSame($renderer, $container['myalias']);
        static::assertSame($renderer, $container['otheralias']);
    }

    public function testGetByCreate()
    {
        $container = new Container();

        $controller = $container->get(JsonRenderer::class);
        static::assertInstanceOf(JsonRenderer::class, $controller);
    }

    public function testGetFail()
    {
        $container = new Container();

        try {
            $container->get('unknown');
            static::fail();
        } catch (NullReferenceException $exception) {
            static::assertEquals('unknown', $exception->getClass());
        }
    }

    public function testDestroy()
    {
        $container = new Container();

        $container->instance('xml', new XmlRenderer());

        static::assertTrue($container->has('xml'));

        $container->destroy('xml');

        static::assertFalse($container->has('xml'));
    }

    public function testDestroyMany()
    {
        $container = new Container();

        $container->instance('xml1', new XmlRenderer());
        $container->instance('xml2', new XmlRenderer());

        static::assertTrue($container->has('xml1'));
        static::assertTrue($container->has('xml2'));

        $container->destroy('xml1', 'xml2');

        static::assertFalse($container->has('xml1'));
        static::assertFalse($container->has('xml2'));
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
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('It cannot be changed; instance', $exception->getMessage());
        }
        try {
            $container->closure('closure', function () {
                return 'closure string change 2';
            });
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('It cannot be changed; closure', $exception->getMessage());
        }
        try {
            $container->alias('alias', 'closure');
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('It cannot be changed; alias', $exception->getMessage());
        }

        // also cannot remove
        try {
            $container->offsetUnset('instance');
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('It cannot be changed; instance', $exception->getMessage());
        }
        try {
            $container->offsetUnset('closure');
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('It cannot be changed; closure', $exception->getMessage());
        }
        try {
            $container->offsetUnset('alias');
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('It cannot be changed; alias', $exception->getMessage());
        }
    }
    
    public function testWith()
    {
        $container = new Container();

        $instance1 = new ArrayObject();
        $instance2 = new ArrayObject();
        $instance3 = new ArrayObject();
        $instance4 = new ArrayObject();

        $container->instance('instance1', $instance1);
        $container->instance('instance2', $instance2);
        $container->instance('instance3', $instance3);
        $container->instance('instance4', $instance4);

        static::assertNotSame($instance1, $instance2); // same is real same?
        static::assertSame($instance1, $container->get('instance1'));
        static::assertSame($instance2, $container->get('instance2'));
        static::assertSame($instance3, $container->get('instance3'));
        static::assertSame($instance4, $container->get('instance4'));

        static::assertSame($container, $container->get(Container::class));
        static::assertSame($container, $container->get(ContainerInterface::class));
        static::assertSame($container, $container->get(InteropContainer::class));
        static::assertSame($container, $container->get('container'));

        $addedInstance1 = new ArrayObject();
        $addedInstance2 = new ArrayObject();
        
        $otherContainer = $container->with([
            'added_instance1' => $addedInstance1,
            'added_instance2' => $addedInstance2,
        ]);

        static::assertNotEquals($otherContainer, $container);

        static::assertFalse($container->has('added_instance1'));
        static::assertFalse($container->has('added_instance2'));

        static::assertTrue($otherContainer->has('added_instance1'));
        static::assertTrue($otherContainer->has('added_instance2'));

        static::assertSame($instance1, $otherContainer->get('instance1'));
        static::assertSame($instance2, $otherContainer->get('instance2'));
        static::assertSame($instance3, $otherContainer->get('instance3'));
        static::assertSame($instance4, $otherContainer->get('instance4'));

        static::assertSame($addedInstance1, $otherContainer->get('added_instance1'));
        static::assertSame($addedInstance2, $otherContainer->get('added_instance2'));

        static::assertSame($otherContainer, $otherContainer->get(Container::class));
        static::assertSame($otherContainer, $otherContainer->get(ContainerInterface::class));
        static::assertSame($otherContainer, $otherContainer->get(InteropContainer::class));
        static::assertSame($otherContainer, $otherContainer->get('container'));
    }
}
