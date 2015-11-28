<?php
namespace Wandu\DI;

use ArrayObject;
use Mockery;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Stub\JsonRenderer;
use Wandu\DI\Stub\XmlRenderer;

class ExtendTest extends TestCase
{
    public function testInstanceExtend()
    {
        $this->container->instance('instance', $renderer = new XmlRenderer());
        $this->container->extend('instance', function ($item) {
            $item->contents = 'instance contents';
            return $item;
        });

        // extended
        $this->assertEquals('instance contents', $this->container['instance']->contents);

        // and equal :-)
        $this->assertSame($renderer, $this->container['instance']);
    }

    public function testClosureExtned()
    {
        $this->container->closure('closure', function () {
            return new JsonRenderer();
        });
        $this->container->extend('closure', function ($item) {
            $item->contents = 'closure contents';
            return $item;
        });

        // extended
        $this->assertEquals('closure contents', $this->container['closure']->contents);

        $this->assertSame($this->container['closure'], $this->container['closure']);
    }

    public function testAliasExtend()
    {
        $this->container = new Container(new ArrayObject());
        $this->container->instance('xml', $renderer = new XmlRenderer);

        $this->container->alias('xml.alias', 'xml');
        $this->container->alias('xml.other.alias', 'xml.alias');

        $this->container->extend('xml.other.alias', function ($item) {
            $item->contents = 'alias contents';
            return $item;
        });

        $this->assertEquals('alias contents', $this->container['xml']->contents);

        // and equal :-)
        $this->assertSame($renderer, $this->container['xml.other.alias']);
    }

    public function testExtendFail()
    {
        try {
            $this->container->extend('unknown', function ($item) {
                return $item .' extended..';
            });
            $this->fail();
        } catch (NullReferenceException $e) {
            $this->assertEquals('unknown', $e->getClass());
        }
    }
}
