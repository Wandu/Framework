<?php
namespace Wandu\Migrator;

use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class MigratorServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(Configuration::class, function (Config $config) {
            return new Configuration([
                'connection' => $config->get('database.migrator.connection'),
                'table' => $config->get('database.migrator.table'),
                'path' => $config->get('database.migrator.path'),
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
