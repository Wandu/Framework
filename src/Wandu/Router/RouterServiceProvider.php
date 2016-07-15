<?php
namespace Wandu\Router;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Router\ClassLoader\WanduLoader;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Responsifier\WanduResponsifier;
use function Wandu\Foundation\config;

class RouterServiceProvider implements  ServiceProviderInterface
{
    public function boot(ContainerInterface $app)
    {
    }

    public function register(ContainerInterface $app)
    {
        $app->bind(ClassLoaderInterface::class, WanduLoader::class);
        $app->bind(ResponsifierInterface::class, WanduResponsifier::class);
        $app->closure(Configuration::class, function () {
            return new Configuration([
                'virtual_method_enabled' => true,
                'cache_disabled' => config('router.cache_disabled', true),
                'cache_file' => config('router.cache_file', null),
            ]);
        });
        $app->bind(Dispatcher::class);
        $app->alias('router', Dispatcher::class);
    }
}
