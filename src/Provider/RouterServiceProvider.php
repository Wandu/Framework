<?php
namespace Wandu\Router\Provider;

use ArrayAccess;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Router\Mapper\WanduMapper;
use Wandu\Router\MapperInterface;
use Wandu\Router\Router;

class RouterServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerInterface $app
     * @param ArrayAccess $config
     * @return self
     */
    public function register(ContainerInterface $app, ArrayAccess $config = null)
    {
        $app->closure(MapperInterface::class, function (ContainerInterface $app) use ($config) {
            return new WanduMapper($app, $config['router.namespace.handler'], $config['router.namespace.middleware']);
        });
        $app->bind(Router::class);
        $app->alias('wandu.router', Router::class);
    }
}
