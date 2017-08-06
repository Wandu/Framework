<?php
namespace Wandu\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Wandu\Console\Contracts\CommandAttachable;
use Wandu\Console\Symfony\CommandAdapter;
use RuntimeException;

class Dispatcher implements CommandAttachable
{
    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    /** @var \Symfony\Component\Console\Application */
    protected $application;

    public function __construct(Application $application, ContainerInterface $container)
    {
        $this->application = $application;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(string $name, $command): SymfonyCommand
    {
        if (is_string($command)) {
            $command = $this->container->get($command);
        }
        if ($command instanceof Command) {
            $command = new CommandAdapter($command);
        }
        if ($command = $this->application->add($command->setName($name))) {
            return $command;
        }
        throw new RuntimeException("cannot attach the command");
    }

    /**
     * @return int
     */
    public function execute()
    {
        return $this->application->run();
    }
}
