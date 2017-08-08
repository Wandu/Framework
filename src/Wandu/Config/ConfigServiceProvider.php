<?php
namespace Wandu\Config;

use Wandu\Config\Contracts\Config as ConfigContract;
use Wandu\Config\Loader\PathLoader;
use Wandu\Config\Loader\YmlLoader;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface 
{
    public function register(ContainerInterface $app)
    {
        $app->bind(ConfigContract::class, Config::class)->after(function (Config $config) {
            $config->pushLoader(new PathLoader([
                new YmlLoader(),
            ]));
            $config->pushLoader(new YmlLoader());
            return $config;
        });
    }

    public function boot(ContainerInterface $app)
    {
    }
}
