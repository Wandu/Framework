<?php
namespace Wandu\Config;

use Wandu\Config\Contracts\Config as ConfigContract;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface 
{
    public function register(ContainerInterface $app)
    {
        $app->bind(ConfigContract::class, Config::class)->after(function (Config $config) {
            return $config->pushLoader();
        });
    }

    public function boot(ContainerInterface $app)
    {
    }
}
