<?php
namespace Wandu\Router;

use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Loader\PsrLoader;
use Wandu\Router\Middleware\Parameterify;
use Wandu\Router\Middleware\Sessionify;
use Wandu\Router\Responsifier\PsrResponsifier;

class RouterServiceProvider implements  ServiceProviderInterface
{
    /** @var \Closure */
    protected $routes;
    
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(LoaderInterface::class, PsrLoader::class);
        $app->bind(ResponsifierInterface::class, PsrResponsifier::class);
        $app->closure(Configuration::class, function (Config $config) {
            return new Configuration([
                'middleware' => $config->get('router.middleware', [Parameterify::class, Sessionify::class]),
                'virtual_method_enabled' => true,
                'cache_disabled' => $config->get('router.cache_disabled', true),
                'cache_file' => $config->get('router.cache_file', null),
            ]);
        });
        $app->bind(Dispatcher::class)->after(function (Dispatcher $dispatcher) {
            if ($this->routes) {
                $dispatcher->setRoutes($this->routes);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
