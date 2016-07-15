<?php
namespace Wandu\Bridges\Latte;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Foundation;
use Wandu\View\Contracts\RenderInterface;
use function Wandu\Foundation\config;

class LatteServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(RenderInterface::class, function ($app) {
            return new LatteView(
                Foundation\path(config('view.path')),
                Foundation\path(config('view.cache'))
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
