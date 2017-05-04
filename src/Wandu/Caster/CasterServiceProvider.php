<?php
namespace Wandu\Caster;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use function Wandu\Foundation\config;

class CasterServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Caster::class, function (ContainerInterface $app) {
            return new Caster(array_map(function ($caster) use ($app) {
                return $app->get($caster);
            }, config('caster.casters', [])));
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
