<?php
namespace Wandu\View\Bridges\Twig;

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
            $loader = new Twig_Loader_Filesystem(config('twig.path', config('view.path')));
            $options = [
                'auto_reload' => true,
            ];
            $cachePath = config('view.cache');
            if ($cachePath) {
                $options['cache'] = $cachePath;
            }
            return new Twig_Environment($loader, $options);
        });
        $app->bind(RenderInterface::class, TwigView::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }
}
