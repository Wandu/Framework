<?php
use Wandu\Config\Config;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Console\Controllers\HelloWandu as ConsoleHelloWorld;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Error\ErrorServiceProvider;
use Wandu\Event\Console\ListenController;
use Wandu\Event\Console\PingController;
use Wandu\Event\EventServiceProvider;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\KernelServiceProvider;
use Wandu\Http\Controllers\HelloWorld as HttpHelloWorld;
use Wandu\Http\HttpServiceProvider;
use Wandu\Http\Middleware\Responsify;
use Wandu\Log\LogServiceProvider;
use Wandu\Q\BeanstalkdQueueServiceProvider;
use Wandu\Router\Router;
use Wandu\Router\RouterServiceProvider;

return new class implements DefinitionInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function providers(ContainerInterface $app)
    {
        $app->instance(Config::class, new Config([
            'debug' => true,
            'log' => [
                'path' => null,
            ]
        ]));
        $app->alias(ConfigInterface::class, Config::class);
        $app->alias('config', Config::class);
        
        $app->register(new KernelServiceProvider());
        $app->register(new HttpServiceProvider()); // HttpRouterKernel
        $app->register(new RouterServiceProvider()); // HttpRouterKernel

        $app->register(new EventServiceProvider());
        $app->register(new BeanstalkdQueueServiceProvider());
        $app->register(new LogServiceProvider());
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
        $dispatcher->command('event:listen', ListenController::class);
        $dispatcher->command('event:ping', PingController::class);
    }
};
