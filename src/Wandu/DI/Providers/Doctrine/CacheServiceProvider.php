<?php
namespace Wandu\DI\Providers\Doctrine;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;
use Memcached;
use Predis\Client;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app)
    {
        $app->closure(Cache::class, function (Config $config, ContainerInterface $app) {
            switch ($config->get('cache.type')) {
                case 'apcu':
                    $cache = new ApcuCache();
                    break;
                case 'memcached':
                    $cache = new MemcachedCache();
                    $cache->setMemcached($app->get(Memcached::class));
                    break;
                case 'redis':
                    $cache = new RedisCache();
                    $cache->setRedis(Client::class);
                    break;
                default:
                    $cache = new FilesystemCache(sys_get_temp_dir());
            }
            $cache->setNamespace('wandu.doctrine.cache.');
            return $cache;
        });
    }

    public function boot(ContainerInterface $app)
    {
    }
}
