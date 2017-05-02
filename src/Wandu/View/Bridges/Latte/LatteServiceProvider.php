<?php
namespace Wandu\View\Bridges\Latte;

use Latte\Engine;
use Latte\Loaders\FileLoader;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\View\Contracts\RenderInterface;
use function Wandu\Foundation\config;

class LatteServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->closure(Engine::class, function () {
            $engine = new Engine();
            $engine->setLoader(new FileLoader());
            $cachePath = config('view.cache');
            if ($cachePath) {
                $engine->setTempDirectory($cachePath);
            }
            return $engine;
        });
        $app->closure(RenderInterface::class, function ($app) {
            return new LatteView(
                $app[Engine::class],
                config('view.path')
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
