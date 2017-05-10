<?php
namespace Wandu\DI\Descriptor;

use PHPUnit_Framework_TestCase;
use Wandu\DI\Container;

class AfterTest extends PHPUnit_Framework_TestCase
{
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
        static::assertEquals('instance contents', $container['instance']->contents);
        static::assertSame($renderer, $container['instance']);
    }
    
    public function testAfterClosure()
    {
        $container = new Container();

        $container->closure('closure', function () {
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

        $container->instance('xml', $renderer = new AfterTestXmlRenderer);

        $container->alias('xml.alias', 'xml');
        $container->alias('xml.other.alias', 'xml.alias');

        // alias..
        $container->descriptor('xml.other.alias')->after(function ($item) {
            $item->contents = 'alias contents';
        });

        static::assertEquals('alias contents', $container['xml']->contents);
        static::assertSame($renderer, $container['xml.other.alias']);
    }

//    public function testAliasAfterPropagation()
//    {
//        $container = new Container();
//
//        // extend first,,
//        $container->descriptor('xml.other.alias')->after(function ($item) {
//            $item->contents = 'alias contents';
//        });
//
//        $container->instance('xml', $renderer = new AfterTestXmlRenderer);
//
//        $container->alias('xml.alias', 'xml');
//        $container->alias('xml.other.alias', 'xml.alias');
//
//        static::assertEquals('alias contents', $container['xml']->contents);
//
//        // and equal :-)
//        static::assertSame($renderer, $container['xml.other.alias']);
//    }
//
//    public function testAfterUnknown()
//    {
//        $container = new Container();
//        $container->descriptor('unknown')->after(function ($item) {
//            return $item .' extended..';
//        });
//    }
}

interface AfterTestRenderable {}
class AfterTestXmlRenderer implements AfterTestRenderable {}
class AfterTestJsonRenderer implements AfterTestRenderable {}
