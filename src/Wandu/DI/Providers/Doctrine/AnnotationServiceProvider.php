<?php
namespace Wandu\DI\Providers\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\Cache;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class AnnotationServiceProvider implements ServiceProviderInterface 
{
    public function register(ContainerInterface $app)
    {
        $app->closure(Reader::class, function (Cache $cache, Config $config) {
            return new CachedReader(new AnnotationReader(), $cache, $config->get('debug', true));
        });
    }

    public function boot(ContainerInterface $app)
    {
    }
}
