<?php
use Wandu\Config\DotConfig;
use Wandu\Console\Controllers\HelloWorld as ConsoleHelloWorld;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\ConfigInterface;
use Wandu\Http\Controllers\HelloWorld as HttpHelloWorld;
use Wandu\Http\Middleware\Responsify;
use Wandu\Router\Router;
use Wandu\Router\RouterServiceProvider;

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
        $router->middleware([
            Responsify::class,
        ], function (Router $router) {
            $router->get('/', HttpHelloWorld::class);
        });
    }

    /**
     * @param \Wandu\Console\Dispatcher $dispatcher
     */
    public function commands(Dispatcher $dispatcher)
    {
        $dispatcher->command('hello', ConsoleHelloWorld::class);
    }
};
