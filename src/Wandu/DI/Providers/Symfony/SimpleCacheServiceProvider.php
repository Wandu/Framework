<?php
namespace Wandu\DI\Providers\Symfony;

use Memcached;
use Predis\Client;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\ApcuCache;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Simple\MemcachedCache;
use Symfony\Component\Cache\Simple\RedisCache;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class SimpleCacheServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(CacheInterface::class, function (Config $config, ContainerInterface $app) {
            switch ($config->get('cache.type')) {
                case 'apcu':
                    return new ApcuCache('wandu.');
                case 'memcached':
                    return new MemcachedCache($app->get(Memcached::class), 'wandu.');
                case 'redis':
                    return new RedisCache($app->get(Client::class), 'wandu.');
                default:
                    return new FilesystemCache('wandu.');
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
