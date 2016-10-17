<?php
namespace Wandu\Bridges\Eloquent;

use Illuminate\Database\Capsule\Manager;
use Wandu\Bridges\Eloquent\Migrator\MigrateAdapter;
use Wandu\Bridges\Eloquent\Migrator\MigrateTemplate;
use Wandu\Database\Migrator\MigrateAdapterInterface;
use Wandu\Database\Migrator\MigrateTemplateInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;

class EloquentServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Manager::class, function () {
            $capsule = new Manager;
            foreach (config('database.connections', []) as $name => $connection) {
                $capsule->addConnection($connection, $name);
            }
            return $capsule;
        });
        $app->alias('database', Manager::class);
        
        $app->bind(MigrateTemplateInterface::class, MigrateTemplate::class);
        $app->bind(MigrateAdapterInterface::class, MigrateAdapter::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        $app->get(Manager::class)->setAsGlobal();
        $app->get(Manager::class)->bootEloquent();
    }
}
