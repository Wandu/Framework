<?php
namespace Wandu\Database;

use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Migrator\MigrateAdapter;
use Wandu\Database\Migrator\MigrateAdapterInterface;
use Wandu\Database\Migrator\MigrateTemplate;
use Wandu\Database\Migrator\MigrateTemplateInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Manager::class, function () {
            $manager = new Manager();
            foreach (config('database.connections', []) as $name => $connection) {
                $manager->connect($connection, $name);
            }
            return $manager;
        });
        $app->alias('database', Manager::class);
        $app->closure(ConnectionInterface::class, function (Manager $manager) {
            return $manager->getConnection();
        });
        $app->alias('connection', ConnectionInterface::class);

        $app->bind(MigrateTemplateInterface::class, MigrateTemplate::class);
        $app->bind(MigrateAdapterInterface::class, MigrateAdapter::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
