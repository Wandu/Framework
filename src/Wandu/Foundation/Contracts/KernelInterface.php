<?php
namespace Wandu\Foundation\Contracts;

use Wandu\DI\ContainerInterface;

interface KernelInterface
{
    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @return void
     */
    public function boot(ContainerInterface $app);

    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @return mixed
     */
    public function execute(ContainerInterface $app);
}
