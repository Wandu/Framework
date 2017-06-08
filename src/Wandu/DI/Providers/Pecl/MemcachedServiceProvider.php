<?php
namespace Wandu\DI\Providers\Pecl;

use Memcached;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class MemcachedServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(Memcached::class)->after(function(Memcached $memcached) use ($app) {
            $config = $app->get(Config::class);
            foreach ($config->get('pecl.memcached.servers', []) as $server) {
                $this->addServer($memcached, $server);
            }
            if ($server = $config->get('pecl.memcached.server')) {
                $this->addServer($memcached, $server);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
    
    private function addServer(Memcached $memcached, array $server)
    {
        $memcached->addServer(
            $server['host'] ?? '127.0.0.1',
            $server['port'] ?? 11211,
            $server['weight'] ?? 0
        );
    }
}
