<?php
namespace Wandu\DI\Stub\Boot;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;

class BootProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app)
    {
        $app->get('mockery')->register();
    }

    public function boot(ContainerInterface $app)
    {
        $app->get('mockery')->boot();
    }
}
