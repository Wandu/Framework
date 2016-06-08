<?php
namespace Wandu\Foundation\Kernels;

use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\Config\Config;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Application;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\Contracts\KernelInterface;

class ConsoleKernel implements KernelInterface
{
    /** @var \Wandu\Foundation\Contracts\DefinitionInterface */
    protected $definition;

    public function __construct(DefinitionInterface $definition)
    {
        $this->definition = $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        $app->instance(Config::class, new Config($this->definition->configs()));
        $app->alias(ConfigInterface::class, Config::class);
        $app->alias('config', Config::class);
        $this->definition->providers($app);
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

        $this->definition->commands($dispatcher);
        $dispatcher->execute();
        return $symfonyApplication->run();
    }
}
