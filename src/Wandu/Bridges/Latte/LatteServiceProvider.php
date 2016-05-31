<?php
namespace Wandu\Bridges\Latte;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\View\Contracts\RenderInterface;

class LatteServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(RenderInterface::class, function ($app) {
            return new LatteView(
                $app['config']->get('view.path'),
                $app['config']->get('view.cache')
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
