<?php
namespace Wandu\Installation;

use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Foundation\Contracts\KernelInterface;
use Wandu\Installation\Commands\InstallCommand;

class InstallServiceProvider implements ServiceProviderInterface 
{
    /**
     * {@inheritdoc}
     */
    public function register(ContainerInterface $app)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        if ($app->has(KernelInterface::class)) {
            /** @var \Wandu\Foundation\Contracts\KernelInterface $kernel */
            $kernel = $app->get(KernelInterface::class);
            $kernel['commands'] = [
                'install' => InstallCommand::class,
            ];
        }
    }
}
