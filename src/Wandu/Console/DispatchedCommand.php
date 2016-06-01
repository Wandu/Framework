<?php
namespace Wandu\Console;

use Interop\Container\ContainerInterface;
use Wandu\Console\Symfony\CommandProxy;

class DispatchedCommand
{
    public function __construct($commandName, $className)
    {
        $this->commandName = $commandName;
        $this->className = $className;
    }

    public function execute(ContainerInterface $container)
    {
        return new CommandProxy($this->commandName, $container->get($this->className));
    }
}
