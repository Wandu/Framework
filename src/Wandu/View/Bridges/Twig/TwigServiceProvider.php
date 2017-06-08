<?php
namespace Wandu\View\Bridges\Twig;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\View\Contracts\RenderInterface;

class TwigServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Twig_Environment::class, function (ConfigInterface $config) {
            $loader = new Twig_Loader_Filesystem($config->get('twig.path', $config->get('view.path')));
            $options = [
                'auto_reload' => true,
            ];
            $cachePath = $config->get('view.cache');
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
