<?php
namespace Wandu\Error;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class ErrorServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(Dispatcher::class);
        $app->alias('error', Dispatcher::class);
    }

    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function boot(ContainerInterface $app)
    {
        $app[Dispatcher::class]->boot();
    }
}
