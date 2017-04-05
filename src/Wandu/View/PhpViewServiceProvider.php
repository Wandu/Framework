<?php
namespace Wandu\View;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\View\Contracts\RenderInterface;
use function Wandu\Foundation\config;

/**
 * @deprecated use PhiewServiceProvider
 */
class PhpViewServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(RenderInterface::class, function () {
            return new PhpView(config('view.path'));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
