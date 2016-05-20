<?php
namespace Wandu\Foundation\Kernels;

use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Application;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\Contracts\KernelInterface;

class ConsoleKernel implements KernelInterface
{
    /** @var \Wandu\Foundation\Contracts\DefinitionInterface */
    protected $config;

    public function __construct(DefinitionInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        $this->config->providers($app);
    }

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

        $this->config->commands($dispatcher);
        $dispatcher->execute();
        return $symfonyApplication->run();
    }
}
