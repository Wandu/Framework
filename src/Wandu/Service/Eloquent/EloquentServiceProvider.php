<?php
namespace Wandu\Service\Eloquent;

use Illuminate\Database\Capsule\Manager;
use Wandu\Config\Contracts\Config;
use Wandu\Migrator\Contracts\Adapter;
use Wandu\Migrator\Contracts\MigrationTemplate;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class EloquentServiceProvider implements ServiceProviderInterface 
{
    public function register(ContainerInterface $app)
    {
        $app->bind(Manager::class)->after(function (Manager $manager, Config $config) {
            foreach ($config->get('database.connections', []) as $name => $connection) {
                $manager->addConnection($connection, $name);
            }
            return $manager;
        });
        $app->bind(Adapter::class, EloquentAdapter::class);
        $app->bind(MigrationTemplate::class, EloquentTemplate::class);
    }

    public function boot(ContainerInterface $app)
    {
        $app->get(Manager::class)->setAsGlobal();
        $app->get(Manager::class)->bootEloquent();
    }
}
