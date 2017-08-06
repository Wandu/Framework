<?php
namespace Wandu\Console;

use Symfony\Component\Console\Application;
use Wandu\DI\ContainerInterface;
use Wandu\DI\ServiceProviderInterface;
use Wandu\Foundation\Application as WanduApplication;

class ConsoleServiceProvider implements ServiceProviderInterface 
{
    /** @var string */
    protected $name = WanduApplication::NAME;
    
    /** @var string */
    protected $version = WanduApplication::VERSION;
    
    public function register(ContainerInterface $app)
    {
        $app->bind(Application::class)->assignMany([
            'name', ['value' => $this->name],
            'version', ['value' => $this->version],
        ]);
    }

    public function boot(ContainerInterface $app)
    {
    }
}
