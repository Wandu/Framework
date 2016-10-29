<?php
namespace Wandu\Bridges\Latte;

use Latte\Engine;
use Latte\Loaders\FileLoader;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\View\Contracts\RenderInterface;
use function Wandu\Foundation\config;
use function Wandu\Foundation\path;

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
                $engine->setTempDirectory(path($cachePath));
            }
            return $engine;
        });
        $app->closure(RenderInterface::class, function ($app) {
            return new LatteView(
                $app[Engine::class],
                path(config('view.path'))
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
