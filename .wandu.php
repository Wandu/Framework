<?php
use Wandu\Config\DotConfig;
use Wandu\Console\Controllers\HelloWorld;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\ConfigInterface;
use Wandu\Router\RouterServiceProvider;
use Wandu\Router\Router;

return new class implements ConfigInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function providers(ContainerInterface $app)
    {
        $app['config'] = new DotConfig([]);

        $app->register(new RouterServiceProvider());
    }

    /**
     * @param \Wandu\Router\Router $router
     */
    public function routes(Router $router)
    {
    }

    /**
     * @param \Wandu\Console\Dispatcher $dispatcher
     */
    public function commands(Dispatcher $dispatcher)
    {
        $dispatcher->command('hello', HelloWorld::class);
    }
};
