<?php
namespace Wandu\Router\Provider;

use ArrayAccess;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Router\Middleware\MapperInterface;

class RouterServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerInterface $app
     * @param ArrayAccess $config
     * @return self
     */
    public function register(ContainerInterface $app, ArrayAccess $config = null)
    {
        $app->closure(MapperInterface::class, function () {

        });
    }
}
