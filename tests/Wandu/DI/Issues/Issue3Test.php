<?php
namespace Wandu\DI\Issues;

use Mockery;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Stub\JsonRenderer;
use Wandu\DI\TestCase;

class Issue3Test extends TestCase
{
    public function testHasSuccess()
    {
        $this->assertTrue($this->container->has(JsonRenderer::class));

        $controller = $this->container->get(JsonRenderer::class);
        $this->assertInstanceOf(JsonRenderer::class, $controller);
    }

    public function testHasFail()
    {
        $this->assertFalse($this->container->has("Wandu\\DI\\Stub\\UnknownRenderer"));

        try {
            $this->container->get("Wandu\\DI\\Stub\\UnknownRenderer");
        } catch (NullReferenceException $e) {
            $this->assertEquals('It was not found; Wandu\DI\Stub\UnknownRenderer', $e->getMessage());
        }
    }

    public function testHasWithPlainVariable()
    {
        $this->container['known'] = true;

        $this->assertFalse($this->container->has('unknown'));
        $this->assertTrue($this->container->has('known'));
    }

    public function testHasWithNull()
    {
        $this->container['known'] = null;

        $this->assertFalse($this->container->has('unknown'));
        $this->assertTrue($this->container->has('known'));
    }
}
