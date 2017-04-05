<?php
namespace Wandu\View;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\View\Contracts\RenderInterface;
use function Wandu\Foundation\config;
use Wandu\View\Phiew\Configuration;
use Wandu\View\Phiew\Contracts\ResolverInterface;
use Wandu\View\Phiew\FileResolver;

class PhiewServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Configuration::class, function () {
            $conf = new Configuration();
            $conf->path[] = 'views';
            return $conf;
        });
        $app->bind(ResolverInterface::class, FileResolver::class);
        $app->bind(RenderInterface::class, Phiew::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
