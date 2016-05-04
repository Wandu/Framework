<?php
namespace Wandu\Foundation;

use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Router\Router;
use Wandu\Router\RoutesInterface;

interface ConfigInterface extends RoutesInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function providers(ContainerInterface $app);

    /**
     * @param \Wandu\Console\Dispatcher $dispatcher
     */
    public function commands(Dispatcher $dispatcher);

    /**
     * @param \Wandu\Router\Router $router
     */
    public function routes(Router $router);
}
