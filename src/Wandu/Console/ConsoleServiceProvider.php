<?php
namespace Wandu\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Foundation\Application;

class ConsoleServiceProvider implements ServiceProviderInterface 
{
    /** @var string */
    protected $name = Application::NAME;
    
    /** @var string */
    protected $version = Application::VERSION;
    
    /** @var array */
    protected $commands = [];
    
    public function register(ContainerInterface $app)
    {
        $app->closure(SymfonyApplication::class, function () {
            return new SymfonyApplication($this->name, $this->version);
        });
        $app->bind(Dispatcher::class)->after(function (Dispatcher $dispatcher) {
            foreach ($this->commands as $name => $command) {
                $dispatcher->add($name, $command);
            }
        });
    }

    public function boot(ContainerInterface $app)
    {
    }
}
