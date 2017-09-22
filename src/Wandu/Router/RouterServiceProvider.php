<?php
namespace Wandu\Router;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProvider;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Loader\PsrLoader;
use Wandu\Router\Responsifier\PsrResponsifier;

class RouterServiceProvider extends ServiceProvider
{
    /** @var array */
    protected $options = [
        //'method_override_enabled' => true,
        //'method_spoofing_enabled' => false,
        //'defined_prefix' => '',
        //'defined_middlewares' => [],
        //'defined_domains' => [],
    ];

    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
        $app->bind(LoaderInterface::class, PsrLoader::class);
        $app->bind(ResponsifierInterface::class, PsrResponsifier::class);
        $app->bind(Dispatcher::class)->assign('options', ['value' => $this->options]);
    }
}
