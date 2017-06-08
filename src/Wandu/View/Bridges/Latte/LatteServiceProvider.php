<?php
namespace Wandu\View\Bridges\Latte;

use Latte\Engine;
use Latte\Loaders\FileLoader;
use Wandu\Config\Contracts\Config;
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
        $app->closure(Engine::class, function (Config $config) {
            $engine = new Engine();
            $engine->setLoader(new FileLoader());
            $cachePath = $config->get('view.cache');
            if ($cachePath) {
                $engine->setTempDirectory($cachePath);
            }
            return $engine;
        });
        $app->closure(RenderInterface::class, function (Engine $engine, Config $config) {
            return new LatteView(
                $engine,
                $config->get('view.path')
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
