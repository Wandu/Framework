<?php
namespace Wandu\Foundation\Bootstrapper;

use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\Bootstrapper;

class ConsoleBootstrapper implements Bootstrapper
{
    /** @var array */
    protected $commands;

    public function __construct(array $commands = [])
    {
        $this->commands = $commands;
    }

    /**
     * {@inheritdoc}
     */
    public function providers(): array
    {
        return [];
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
