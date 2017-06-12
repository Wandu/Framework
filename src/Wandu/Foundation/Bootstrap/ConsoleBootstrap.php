<?php
namespace Wandu\Foundation\Bootstrap;

use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\Config\ConfigServiceProvider;
use Wandu\Console\ConsoleServiceProvider;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\Bootstrap;

class ConsoleBootstrap implements Bootstrap
{
    /**
     * {@inheritdoc}
     */
    public function providers(): array
    {
        return [
            new ConfigServiceProvider(),
            new ConsoleServiceProvider(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContainerInterface $app): int
    {
        $app->get(Dispatcher::class)->execute();
        return $app->get(SymfonyApplication::class)->run();
    }
}
