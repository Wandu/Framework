<?php
namespace Wandu\DI;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\NullReferenceException;

class ContainerTest extends TestCase 
{
    public function testConstruct()
    {
        $container = new Container();

        static::assertSame($container, $container->get('container'));
        static::assertSame($container, $container->get(Container::class));
        static::assertSame($container, $container->get(ContainerInterface::class));
    }

    public function testHas()
    {
        $container = new Container();

        $container->instance(ContainerTestRenderable::class, new ContainerTestXmlRenderer);

        static::assertTrue($container->has(ContainerTestRenderable::class)); // set by instance
        static::assertTrue($container->has(ContainerTestJsonRenderer::class)); // class true
        static::assertFalse($container->has(ContainerTestServerAccessible::class)); // interface false
        static::assertFalse($container->has('Unknown\\Class')); // not defined class

        // "has" map to offsetExists
        static::assertTrue(isset($container[ContainerTestRenderable::class]));
        static::assertFalse(isset($container[ContainerTestServerAccessible::class]));
        static::assertTrue(isset($container[ContainerTestJsonRenderer::class]));
    }

    public function testHasNull()
    {
        $container = new Container();

        $container->instance('null', null);

        static::assertTrue($container->has('null')); // container has null,
        static::assertFalse($container->has('undefined'));

        // "has" map to offsetExists but except null.
        static::assertFalse(isset($container['null']));
        static::assertFalse(isset($container['undefined']));
    }

    public function testInstance()
    {
        $container = new Container();
        $xml = new ContainerTestXmlRenderer();
        
        $container->instance('xml', $xml);
        $container->instance('is_debug', true);

        static::assertSame($xml, $container->get('xml'));
        static::assertSame(true, $container->get('is_debug'));

        // "get" map to offsetGet
        static::assertSame($xml, $container['xml']);
        static::assertSame(true, $container['is_debug']);
    }

    public function testClosure()
    {
        $container = new Container();

        $container->instance(ContainerTestRenderable::class, $renderer = new ContainerTestXmlRenderer());
        $container->closure(ContainerTestHttpController::class, function (ContainerInterface $app) {
            return new ContainerTestHttpController($app[ContainerTestRenderable::class], [
                'username' => 'username string',
                'password' => 'password string',
            ]);
        });

        static::assertInstanceOf(ContainerTestHttpController::class, $container[ContainerTestHttpController::class]);
        static::assertSame($container[ContainerTestHttpController::class], $container[ContainerTestHttpController::class]);
        static::assertSame($renderer, $container[ContainerTestHttpController::class]->renderer);
        static::assertEquals([
            'username' => 'username string',
            'password' => 'password string',
        ], $container[ContainerTestHttpController::class]->config);
    }

    public function testClosureWithTypeHint()
    {
        $container = new Container();

        $container->instance(ContainerTestRenderable::class, $renderer = new ContainerTestXmlRenderer());
        $container->closure(ContainerTestHttpController::class, function (ContainerTestRenderable $renderable) {
            return new ContainerTestHttpController($renderable, [
                'username' => 'username string',
                'password' => 'password string',
            ]);
        });

        static::assertInstanceOf(ContainerTestHttpController::class, $container[ContainerTestHttpController::class]);
        static::assertSame($container[ContainerTestHttpController::class], $container[ContainerTestHttpController::class]);
        static::assertSame($renderer, $container[ContainerTestHttpController::class]->renderer);
        static::assertEquals([
            'username' => 'username string',
            'password' => 'password string',
        ], $container[ContainerTestHttpController::class]->config);
    }

    public function testAlias()
    {
        $container = new Container();
        $renderer = new ContainerTestXmlRenderer;

        $container->instance(ContainerTestRenderable::class, $renderer);

        $container->alias('myalias', ContainerTestRenderable::class);
        $container->alias('otheralias', 'myalias');

        static::assertSame($renderer, $container[ContainerTestRenderable::class]);
        static::assertSame($renderer, $container['myalias']);
        static::assertSame($renderer, $container['otheralias']);
    }

    public function testGetByCreate()
    {
        $container = new Container();

        $controller = $container->get(ContainerTestJsonRenderer::class);
        static::assertInstanceOf(ContainerTestJsonRenderer::class, $controller);
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

        $container->instance('xml', new ContainerTestXmlRenderer());

        static::assertTrue($container->has('xml'));

        $container->destroy('xml');

        static::assertFalse($container->has('xml'));
    }

    public function testDestroyMany()
    {
        $container = new Container();

        $container->instance('xml1', new ContainerTestXmlRenderer());
        $container->instance('xml2', new ContainerTestXmlRenderer());

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
            static::assertEquals('it cannot be changed; "instance".', $exception->getMessage());
        }
        try {
            $container->closure('closure', function () {
                return 'closure string change 2';
            });
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('it cannot be changed; "closure".', $exception->getMessage());
        }
        try {
            $container->alias('alias', 'closure');
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('it cannot be changed; "alias".', $exception->getMessage());
        }

        // also cannot remove
        try {
            $container->offsetUnset('instance');
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('it cannot be changed; "instance".', $exception->getMessage());
        }
        try {
            $container->offsetUnset('closure');
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('it cannot be changed; "closure".', $exception->getMessage());
        }
        try {
            $container->offsetUnset('alias');
            static::fail();
        } catch (CannotChangeException $exception) {
            static::assertEquals('it cannot be changed; "alias".', $exception->getMessage());
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
        static::assertSame($otherContainer, $otherContainer->get('container'));
    }
}

interface ContainerTestRenderable {}
class ContainerTestJsonRenderer implements ContainerTestRenderable {}
class ContainerTestXmlRenderer implements ContainerTestRenderable {}

interface ContainerTestServerAccessible {}

class ContainerTestHttpController
{
    public function __construct(ContainerTestRenderable $renderer, array $config)
    {
        $this->renderer = $renderer;
        $this->config = $config;
    }
}
