<?php
namespace Wandu\DI;

use Wandu\DI\Stub\JsonRenderer;
use Wandu\DI\Stub\XmlRenderer;
use PHPUnit_Framework_TestCase;

class ExtendTest extends PHPUnit_Framework_TestCase
{
    public function testInstanceExtend()
    {
        $container = new Container();
        
        $container->instance('instance', $renderer = new XmlRenderer());
        $container->extend('instance', function ($item) {
            $item->contents = 'instance contents';
            return $item;
        });

        // extended
        $this->assertEquals('instance contents', $container['instance']->contents);

        // and equal :-)
        $this->assertSame($renderer, $container['instance']);
    }

    public function testClosureExtned()
    {
        $container = new Container();

        $container->closure('closure', function () {
            return new JsonRenderer();
        });
        $container->extend('closure', function ($item) {
            $item->contents = 'closure contents';
            return $item;
        });

        // extended
        $this->assertEquals('closure contents', $container['closure']->contents);

        $this->assertSame($container['closure'], $container['closure']);
    }

    public function testAliasExtend()
    {
        $container = new Container();
        
        $container->instance('xml', $renderer = new XmlRenderer);

        $container->alias('xml.alias', 'xml');
        $container->alias('xml.other.alias', 'xml.alias');

        $container->extend('xml.other.alias', function ($item) {
            $item->contents = 'alias contents';
            return $item;
        });

        $this->assertEquals('alias contents', $container['xml']->contents);

        // and equal :-)
        $this->assertSame($renderer, $container['xml.other.alias']);
    }

    public function testAliasExtendPropagation()
    {
        $container = new Container();

        // extend first,,
        $container->extend('xml.other.alias', function ($item) {
            $item->contents = 'alias contents';
            return $item;
        });

        $container->instance('xml', $renderer = new XmlRenderer);

        $container->alias('xml.alias', 'xml');
        $container->alias('xml.other.alias', 'xml.alias');
        
        $this->assertEquals('alias contents', $container['xml']->contents);

        // and equal :-)
        $this->assertSame($renderer, $container['xml.other.alias']);
    }

    public function testExtendUnknown()
    {
        $container = new Container();

        $container->extend('unknown', function ($item) {
            return $item .' extended..';
        });
    }
}
