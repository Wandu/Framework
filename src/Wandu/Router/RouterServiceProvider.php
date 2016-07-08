<?php
namespace Wandu\Router;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Router\ClassLoader\WanduLoader;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Responsifier\WanduResponsifier;

class RouterServiceProvider implements  ServiceProviderInterface
{
    public function boot(ContainerInterface $app)
    {
    }

    public function register(ContainerInterface $app)
    {
        $app->bind(ClassLoaderInterface::class, WanduLoader::class);
        $app->bind(ResponsifierInterface::class, WanduResponsifier::class);
        $app->closure(Configuration::class, function ($app) {
            return new Configuration([
                'virtual_method_enabled' => true,
                'cache_disabled' => $app['config']->get('router.cache_disabled', true),
                'cache_file' => $app['config']->get('router.cache_file', null),
            ]);
        });
        $app->bind(Dispatcher::class);
        $app->alias('router', Dispatcher::class);
    }
}
