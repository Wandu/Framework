<?php
namespace Wandu\Foundation\Contracts;

use Wandu\DI\ContainerInterface;

interface Bootstrapper
{
    /**
     * @return \Wandu\DI\ServiceProviderInterface[]
     */
    public function providers(): array;

    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @return void
     */
    public function boot(ContainerInterface $app);

    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @return int
     */
    public function execute(ContainerInterface $app): int;
}
