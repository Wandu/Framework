<?php
namespace Wandu\Foundation;

use Wandu\Console\ConfigInterface as ConsoleConfigInterface;
use Wandu\DI\ContainerInterface;

interface ConfigInterface extends ConsoleConfigInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     */
    public function register(ContainerInterface $app);
}
