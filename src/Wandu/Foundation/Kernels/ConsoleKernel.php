<?php
namespace Wandu\Foundation\Kernels;

use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Application;
use Wandu\Foundation\ConfigInterface;
use Wandu\Foundation\KernelInterface;

class ConsoleKernel implements KernelInterface
{
    /** @var \Wandu\Foundation\ConfigInterface */
    protected $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        $this->config->register($app);
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
