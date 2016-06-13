<?php
namespace Wandu\Foundation\Definitions;

use Wandu\Console\Commands\PsyshCommand;
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Installation\Commands\InstallCommand;
use Wandu\Router\Router;

class NeedInstallDefinition implements DefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public function configs()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function providers(ContainerInterface $app)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function commands(Dispatcher $dispatcher)
    {
        $dispatcher->add('install', InstallCommand::class);
        $dispatcher->add('psysh', PsyshCommand::class);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router)
    {
    }
}
