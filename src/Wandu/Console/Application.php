<?php
namespace Wandu\Console;

use Wandu\Console\Output\StdOutput;
use Wandu\DI\Container;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends Container
{
    const NAME = "Wandu";
    const VERSION = "0.1";

    /** @var \Wandu\Console\ConfigInterface */
    protected $config;

    public function __construct(ConfigInterface $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    public function execute()
    {
        // config..
        $this->config->register($this);
        $this->boot();

        $dispatcher = new Dispatcher(
            $this,
            $symfonyApplication = new SymfonyApplication(static::NAME, static::VERSION)
        );

        $this->config->commands($dispatcher);
        $dispatcher->execute();
        $symfonyApplication->run();
    }
}
