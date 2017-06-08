<?php
namespace Wandu\Database\Migrator;

use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class MigratorServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Configuration::class, function (ConfigInterface $config) {
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
