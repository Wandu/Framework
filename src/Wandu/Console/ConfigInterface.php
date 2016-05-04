<?php
namespace Wandu\Console;

use Wandu\DI\ContainerInterface;

interface ConfigInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function register(ContainerInterface $app);

    /**
     * @param \Wandu\Console\Dispatcher $dispatcher
     */
    public function commands(Dispatcher $dispatcher);
}
