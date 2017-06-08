<?php
namespace Wandu\Caster;

use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class CasterServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Caster::class, function (ContainerInterface $app, Config $config) {
            return new Caster(array_map(function ($caster) use ($app) {
                return $app->get($caster);
            }, $config->get('caster.casters', [])));
        });
        $app->alias(CastManagerInterface::class, Caster::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
