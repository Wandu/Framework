<?php
namespace Wandu\DI\Methods;

use PHPUnit_Framework_TestCase;
use Wandu\DI\Container;

class ExtendTest extends PHPUnit_Framework_TestCase
{
    public function testInstanceExtend()
    {
        $container = new Container();
        
        $container->instance('instance', $renderer = new ExtendTestXmlRenderer());
        $container->extend('instance', function ($item) {
            $item->contents = 'instance contents';
            return $item;
        });

        // extended
        static::assertEquals('instance contents', $container['instance']->contents);

        // and equal :-)
        static::assertSame($renderer, $container['instance']);
    }

    public function testClosureExtend()
    {
        $container = new Container();

        $container->closure('closure', function () {
            return new ExtendTestJsonRenderer();
        });
        $container->extend('closure', function ($item) {
            $item->contents = 'closure contents';
            return $item;
        });

        // extended
        static::assertEquals('closure contents', $container['closure']->contents);

        static::assertSame($container['closure'], $container['closure']);
    }

    public function testAliasExtend()
    {
        $container = new Container();
        
        $container->instance('xml', $renderer = new ExtendTestXmlRenderer);

        $container->alias('xml.alias', 'xml');
        $container->alias('xml.other.alias', 'xml.alias');

        $container->extend('xml.other.alias', function ($item) {
            $item->contents = 'alias contents';
            return $item;
        });

        static::assertEquals('alias contents', $container['xml']->contents);

        // and equal :-)
        static::assertSame($renderer, $container['xml.other.alias']);
    }

    public function testAliasExtendPropagation()
    {
        $container = new Container();

        // extend first,,
        $container->extend('xml.other.alias', function ($item) {
            $item->contents = 'alias contents';
            return $item;
        });

        $container->instance('xml', $renderer = new ExtendTestXmlRenderer);

        $container->alias('xml.alias', 'xml');
        $container->alias('xml.other.alias', 'xml.alias');
        
        static::assertEquals('alias contents', $container['xml']->contents);

        // and equal :-)
        static::assertSame($renderer, $container['xml.other.alias']);
    }

    public function testExtendUnknown()
    {
        $container = new Container();

        $container->extend('unknown', function ($item) {
            return $item .' extended..';
        });
    }
}

interface ExtendTestRenderable {}
class ExtendTestXmlRenderer implements ExtendTestRenderable {}
class ExtendTestJsonRenderer implements ExtendTestRenderable {}
