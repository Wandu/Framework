<?php
namespace Wandu\Bridges\Twig;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\View\Contracts\RenderInterface;
use function Wandu\Foundation\config;

class TwigServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Twig_Environment::class, function () {
            $loader = new Twig_Loader_Filesystem(config('view.path'));
            $options = [];
            $cachePath = config('view.cache');
            if ($cachePath) {
                $options['cache'] = $cachePath;
            }
            return new Twig_Environment($loader, $options);
        });
        $app->closure(RenderInterface::class, function ($app) {
            return new TwigView($app[Twig_Environment::class]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
