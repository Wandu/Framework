<?php
namespace Wandu\Providers;

use Illuminate\Database\Capsule\Manager;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Manager::class, function (ContainerInterface $app) {
            $capsule = new Manager;
            foreach ($app['config']->get('database.connections') as $name => $connection) {
                $capsule->addConnection($connection, $name);
            }
            return $capsule;
        });
        $app->alias('database', Manager::class);
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
