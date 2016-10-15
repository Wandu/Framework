<?php
namespace Wandu\Database\Migrator;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;
use function Wandu\Foundation\path;

class MigratorServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Configuration::class, function () {
            return new Configuration([
                'connection' => config('database.migrator.connection'),
                'table' => config('database.migrator.table'),
                'path' => path(config('database.migrator.path')),
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
