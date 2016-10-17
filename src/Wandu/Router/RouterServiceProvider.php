<?php
namespace Wandu\Router;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Router\ClassLoader\WanduLoader;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Middleware\Parameterify;
use Wandu\Router\Middleware\Sessionify;
use Wandu\Router\Responsifier\WanduResponsifier;
use function Wandu\Foundation\config;
use function Wandu\Foundation\path;

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
                'middleware' => config('router.middleware', [Parameterify::class, Sessionify::class]),
                'virtual_method_enabled' => true,
                'cache_disabled' => config('router.cache_disabled', true),
                'cache_file' => path(config('router.cache_file', null)),
            ]);
        });
        $app->bind(Dispatcher::class);
        $app->alias('router', Dispatcher::class);
    }
}
