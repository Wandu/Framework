<?php
namespace Wandu\DI\Descriptor;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;

class AfterTest extends TestCase
{
    use Assertions;
    
    public function testAfterBind()
    {
        $container = new Container();
        $container->bind(AfterTestXmlRenderer::class)->after(function ($item) {
            $item->contents = 'bind contents';
        });

        // after
        static::assertInstanceOf(AfterTestXmlRenderer::class, $container[AfterTestXmlRenderer::class]);
        static::assertEquals('bind contents', $container[AfterTestXmlRenderer::class]->contents);
    }

    public function testAfterInstance()
    {
        $container = new Container();
        $container->instance('instance', $renderer = new AfterTestXmlRenderer())->after(function ($item) {
            $item->contents = 'instance contents';
        });

        // after
        static::assertFalse(isset($container['instance']->contents));
        static::assertSame($renderer, $container['instance']);
    }
    
    public function testAfterBindClosure()
    {
        $container = new Container();

        $container->bind('closure', function () {
            return new AfterTestJsonRenderer();
        })->after(function ($item) {
            $item->contents = 'closure contents';
        });

        // after
        static::assertEquals('closure contents', $container['closure']->contents);
        static::assertSame($container['closure'], $container['closure']);
    }

    public function testAliasAfter()
    {
        $container = new Container();

        $container->bind('xml', AfterTestXmlRenderer::class);

        $container->alias('xml.alias', 'xml');
        $container->alias('xml.other.alias', 'xml.alias');

        // alias..
        $container->descriptor('xml.other.alias')->after(function ($item) {
            $item->contents = 'alias contents';
        });

        static::assertEquals('alias contents', $container['xml']->contents);
        static::assertInstanceOf(AfterTestXmlRenderer::class, $container['xml.other.alias']);
    }
}

interface AfterTestRenderable {}
class AfterTestXmlRenderer implements AfterTestRenderable {}
class AfterTestJsonRenderer implements AfterTestRenderable {}
