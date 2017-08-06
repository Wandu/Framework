<?php
namespace Wandu\Foundation\ConsoleApp;

use Wandu\Config\ConfigServiceProvider;
use Wandu\Config\Contracts\Config;
use Wandu\Console\ConsoleServiceProvider;
use Wandu\Console\Contracts\CommandAttachable;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\Bootstrap as BootstrapAbstract;

abstract class Bootstrap implements BootstrapAbstract
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
        $this->registerConfiguration($app->get(Config::class));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContainerInterface $app): int
    {
        $dispatcher = $app->get(Dispatcher::class);
        $this->registerCommands($dispatcher);
        return $dispatcher->execute();
    }

    /**
     * @param \Wandu\Console\Contracts\CommandAttachable $manager
     */
    abstract public function registerCommands(CommandAttachable $manager);

    /**
     * @param \Wandu\Config\Contracts\Config $config
     */
    abstract public function registerConfiguration(Config $config);
}
