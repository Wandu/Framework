<?php
namespace Wandu\Foundation\Contracts;

use Wandu\DI\ContainerInterface;

interface Bootstrapper
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @param \Wandu\Foundation\Contracts\Definition $definition
     * @return void
     */
    public function boot(ContainerInterface $app, Definition $definition);

    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @param \Wandu\Foundation\Contracts\Definition $definition
     * @return int
     */
    public function execute(ContainerInterface $app, Definition $definition): int;
}
