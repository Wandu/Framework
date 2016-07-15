<?php
namespace Wandu\Bridges\Eloquent;

use Illuminate\Database\Capsule\Manager;
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
        $app->closure(Manager::class, function (ContainerInterface $app) {
            $capsule = new Manager;
            foreach (config('database.connections', []) as $name => $connection) {
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
