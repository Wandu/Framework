<?php
namespace Wandu\Foundation\Kernels;

use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Application;

class ConsoleKernel extends KernelAbstract
{
    /**
     * {@inheritdoc}
     */
    public function execute(ContainerInterface $app)
    {
        $dispatcher = new Dispatcher(
            $app,
            $symfonyApplication = new SymfonyApplication(
                Application::NAME,
                Application::VERSION
            )
        );

        $commands = isset($this->attributes['commands']) ? $this->attributes['commands'] : [];
        foreach ($commands as $name => $command) {
            $dispatcher->add($name, $command);
        }
        $dispatcher->execute();
        return $symfonyApplication->run();
    }
}
