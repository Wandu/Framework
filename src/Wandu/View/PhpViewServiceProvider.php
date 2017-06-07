<?php
namespace Wandu\View;

use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\View\Contracts\RenderInterface;

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
        $app->closure(RenderInterface::class, function (ConfigInterface $config) {
            return new PhpView($config->get('view.path'));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
