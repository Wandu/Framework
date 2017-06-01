<?php
namespace Wandu\Router;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Loader\PsrLoader;
use Wandu\Router\Middleware\Parameterify;
use Wandu\Router\Middleware\Sessionify;
use Wandu\Router\Responsifier\PsrResponsifier;
use function Wandu\Foundation\config;

class RouterServiceProvider implements  ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(LoaderInterface::class, PsrLoader::class);
        $app->bind(ResponsifierInterface::class, PsrResponsifier::class);
        $app->closure(Configuration::class, function () {
            return new Configuration([
                'middleware' => config('router.middleware', [Parameterify::class, Sessionify::class]),
                'virtual_method_enabled' => true,
                'cache_disabled' => config('router.cache_disabled', true),
                'cache_file' => config('router.cache_file', null),
            ]);
        });
        $app->bind(Dispatcher::class);
        $app->alias('router', Dispatcher::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
