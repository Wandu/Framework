<?php
namespace Wandu\Console;

use Wandu\DI\ContainerInterface;

interface ConfigInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function register(ContainerInterface $app);

    public function commands(Dispatcher $dispatcher);
}
