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
use Wandu\Database\Console\MigrateStatusCommand;
use Wandu\Database\Console\MigrateUpCommand;
use Wandu\DI\ContainerInterface;
use Wandu\Event\EventServiceProvider;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Http\HttpServiceProvider;
use Wandu\Router\Router;
use Wandu\Router\RouterServiceProvider;

class StandardDefinition implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public function configs()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function providers(ContainerInterface $app)
    {
        $app->register(new HttpServiceProvider());
        $app->register(new RouterServiceProvider());
        $app->register(new EventServiceProvider());
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
