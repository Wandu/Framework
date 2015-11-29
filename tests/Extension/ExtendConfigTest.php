<?php
namespace Wandu\DI\Extension;

use Mockery;
use Wandu\DI\TestCase;

class ExtendConfigTest extends TestCase
{
    public function testConfigLoad()
    {
        $this->container->closure(ExtendConfig::class, function () {
            return new ExtendConfig(dirname(__DIR__).'/Stub');
        });

        // how to works.
        $configLoader = $this->container->get(ExtendConfig::class);

        $this->assertEquals(
            'hello_world',
            call_user_func($configLoader, 'config.app')
        );

        // more simple way.
        $this->container->alias('config', ExtendConfig::class);
        $this->assertEquals(
            'hello_world',
            $this->container->config('config.app')
        );
    }

    public function testConfigWithDefault()
    {
        $this->container->closure(ExtendConfig::class, function () {
            return new ExtendConfig(dirname(__DIR__).'/Stub');
        });
        $this->container->alias('config', ExtendConfig::class);

        $this->assertEquals(
            'hello_world',
            $this->container->config('config.app', 'default')
        );

        $this->assertEquals(
            null,
            $this->container->config('config.unknown')
        );

        $this->assertEquals(
            'default',
            $this->container->config('config.unknown', 'default')
        );
    }
}
