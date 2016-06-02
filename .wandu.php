<?php
use Wandu\Bridges\Eloquent\EloquentServiceProvider;
use Wandu\Bridges\Latte\LatteServiceProvider;
use Wandu\Bridges\Monolog\MonologServiceProvider;
use Wandu\Config\Config;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Console\Dispatcher;
use Wandu\Database\Console\MigrateCommand;
use Wandu\Database\Console\MigrateCreateCommand;
use Wandu\DI\ContainerInterface;
use Wandu\Event\Commands\ListenCommand;
use Wandu\Event\Commands\PingCommand;
use Wandu\Event\EventServiceProvider;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\KernelServiceProvider;
use Wandu\Http\HttpServiceProvider;
use Wandu\Q\BeanstalkdQueueServiceProvider;
use Wandu\Router\Controllers\HelloWorldController;
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
            'database' => [
                'connections' => [],
                'migration' => [
                    'path' => 'migrations',
                ],
            ],
            'log' => [
                'path' => null,
            ],
            'view' => [
                'path' => 'views',
                'cache' => 'cache/views',
            ]
        ]));
        $app->alias(ConfigInterface::class, Config::class);
        $app->alias('config', Config::class);
        
        $app->register(new KernelServiceProvider());
        $app->register(new HttpServiceProvider()); // HttpRouterKernel
        $app->register(new RouterServiceProvider()); // HttpRouterKernel
        $app->register(new EventServiceProvider());
        $app->register(new BeanstalkdQueueServiceProvider());
        $app->register(new MonologServiceProvider());
        $app->register(new EloquentServiceProvider());
        $app->register(new LatteServiceProvider());
    }

    /**
     * @param \Wandu\Router\Router $router
     */
    public function routes(Router $router)
    {
        $router->get('/', HelloWorldController::class);
    }

    /**
     * @param \Wandu\Console\Dispatcher $dispatcher
     */
    public function commands(Dispatcher $dispatcher)
    {
        $dispatcher->add('event:listen', ListenCommand::class);
        $dispatcher->add('event:ping', PingCommand::class);

        $dispatcher->add('migrate', MigrateCommand::class);
        $dispatcher->add('migrate:rollback', MigrateCommand::class);
        $dispatcher->add('migrate:create', MigrateCreateCommand::class);
    }
};
