<?php
namespace Wandu\Foundation\Definitions;

use Wandu\Bridges\Eloquent\EloquentServiceProvider;
use Wandu\Bridges\Latte\LatteServiceProvider;
use Wandu\Bridges\Monolog\MonologServiceProvider;
use Wandu\Console\Commands\PsyshCommand;
use Wandu\Console\Dispatcher;
use Wandu\Database\Console\MigrateCommand;
use Wandu\Database\Console\MigrateCreateCommand;
use Wandu\Database\Console\MigrateDownCommand;
use Wandu\Database\Console\MigrateRollbackCommand;
use Wandu\Database\Console\MigrateUpCommand;
use Wandu\Database\Console\MigrateStatusCommand;
use Wandu\DI\ContainerInterface;
use Wandu\Event\Commands\ListenCommand;
use Wandu\Event\Commands\PingCommand;
use Wandu\Event\EventServiceProvider;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\KernelServiceProvider;
use Wandu\Http\HttpServiceProvider;
use Wandu\Q\BeanstalkdQueueServiceProvider;
use Wandu\Router\Router;
use Wandu\Router\RouterServiceProvider;

class FullDefinition implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public function configs()
    {
        return [
            'debug' => true,
            'database' => [
                'connections' => [
                    'default' => [
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'database'  => 'allbus',
                        'username'  => 'root',
                        'password'  => 'root',
                        'charset'   => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix'    => 'local_',
                    ],
                ],
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function providers(ContainerInterface $app)
    {
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
     * {@inheritdoc}
     */
    public function commands(Dispatcher $dispatcher)
    {
        $dispatcher->add('psysh', PsyshCommand::class);

        $dispatcher->add('event:listen', ListenCommand::class);
        $dispatcher->add('event:ping', PingCommand::class);

        // migrates
        $dispatcher->add('migrate:create', MigrateCreateCommand::class);
        $dispatcher->add('migrate:status', MigrateStatusCommand::class);

        $dispatcher->add('migrate', MigrateCommand::class);
        $dispatcher->add('migrate:rollback', MigrateRollbackCommand::class);

        $dispatcher->add('migrate:up', MigrateUpCommand::class);
        $dispatcher->add('migrate:down', MigrateDownCommand::class);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router)
    {
    }
}
