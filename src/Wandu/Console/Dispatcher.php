<?php
namespace Wandu\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\Console\Symfony\CommandAdapter;
use Wandu\DI\Exception\NullReferenceException;

class Dispatcher
{
    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    /** @var \Symfony\Component\Console\Application */
    protected $application;

    /** @var string[] */
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
     */
    public function add($name, $className)
    {
        $this->commands[$name] = $className;
    }

    public function execute()
    {
        foreach ($this->commands as $name => $command) {
            try {
                $this->application->add(
                    new CommandAdapter($name, $this->container->get($command))
                );
            } catch (NullReferenceException $e) { // typo is continue
                continue;
            }
            
        }
    }
}
