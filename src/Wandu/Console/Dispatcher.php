<?php
namespace Wandu\Console;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application as SymfonyApplication;

class Dispatcher
{
    /** @var \Interop\Container\ContainerInterface */
    protected $container;

    /** @var \Symfony\Component\Console\Application */
    protected $application;

    /** @var \Wandu\Console\DispatchedCommand[] */
    protected $commands;

    public function __construct(ContainerInterface $container, SymfonyApplication $application)
    {
        $this->container = $container;
        $this->application = $application;
        $this->commands = [];
    }

    /**
     * @param string $name
     * @param string $className
     * @return \Wandu\Console\DispatchedCommand
     */
    public function command($name, $className)
    {
        return $this->commands[] = new DispatchedCommand($name, $className);
    }

    public function execute()
    {
        foreach ($this->commands as $command) {
            $this->application->add($command->execute($this->container));
        }
    }
}
