<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Wandu\Config\Contracts\Config;
use Wandu\Database\Contracts\Connection;
use Wandu\Database\Contracts\Entity\MetadataReaderInterface;
use Wandu\Database\Contracts\Migrator\MigrateAdapterInterface;
use Wandu\Database\Contracts\Migrator\MigrationTemplateInterface;
use Wandu\Database\Entity\MetadataReader;
use Wandu\Database\Migrator\MigrateAdapter;
use Wandu\Database\Migrator\MigrationTemplate;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(MetadataReaderInterface::class, MetadataReader::class);
        $app->bind(DatabaseManager::class)->after(function (DatabaseManager $manager) use ($app) {
            foreach ($app->get(Config::class)->get('database.connections', []) as $name => $connection) {
                $manager->connect($connection, $name);
            }
        });
        $app->bind(MigrationTemplateInterface::class, MigrationTemplate::class);
        $app->bind(MigrateAdapterInterface::class, MigrateAdapter::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
