<?php
namespace Wandu\DI\Issues;

use Wandu\DI\Container;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Stub\JsonRenderer;
use PHPUnit_Framework_TestCase;

class Issue3Test extends PHPUnit_Framework_TestCase
{
    public function testHasSuccess()
    {
        $container = new Container();
        
        $this->assertTrue($container->has(JsonRenderer::class));

        $controller = $container->get(JsonRenderer::class);
        $this->assertInstanceOf(JsonRenderer::class, $controller);
    }

    public function testHasFail()
    {
        $container = new Container();

        $this->assertFalse($container->has("Wandu\\DI\\Stub\\UnknownRenderer"));

        try {
            $container->get("Wandu\\DI\\Stub\\UnknownRenderer");
        } catch (NullReferenceException $e) {
            $this->assertEquals('It was not found; Wandu\DI\Stub\UnknownRenderer', $e->getMessage());
        }
    }

    public function testHasWithPlainVariable()
    {
        $container = new Container();

        $container['known'] = true;

        $this->assertFalse($container->has('unknown'));
        $this->assertTrue($container->has('known'));
    }

    public function testHasWithNull()
    {
        $container = new Container();

        $container['known'] = null;

        $this->assertFalse($container->has('unknown'));
        $this->assertTrue($container->has('known'));
    }
}
