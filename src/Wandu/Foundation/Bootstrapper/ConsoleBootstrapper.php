<?php
namespace Wandu\Foundation\Bootstrapper;

use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Application;
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
        $dispatcher = new Dispatcher(
            $app,
            $symfonyApplication = new SymfonyApplication(
                Application::NAME,
                Application::VERSION
            )
        );

        foreach ($this->commands as $name => $command) {
            $dispatcher->add($name, $command);
        }
        $dispatcher->execute();
        return $symfonyApplication->run();
    }
}
