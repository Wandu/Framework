<?php
namespace Wandu\Router;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Router\ClassLoader\WanduLoader;
use Wandu\Router\Contracts\ClassLoaderInterface;

class RouterServiceProvider implements  ServiceProviderInterface
{
    public function boot(ContainerInterface $app)
    {
    }

    public function register(ContainerInterface $app)
    {
        $app->bind(ClassLoaderInterface::class, WanduLoader::class);
        $app->closure(Dispatcher::class, function (ContainerInterface $app) {
            return new Dispatcher($app[ClassLoaderInterface::class], [
                'virtual_method_enabled' => true,
                'cache_disabled' => $app['config']->get('router.cache_disabled', true),
                'cache_file' => $app['config']->get('router.cache_file', null),
            ]);
        });
        $app->alias('router', Dispatcher::class);
    }
}
