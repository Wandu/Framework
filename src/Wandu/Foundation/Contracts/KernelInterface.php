<?php
namespace Wandu\Foundation\Contracts;

use ArrayAccess;
use Wandu\DI\ContainerInterface;

interface KernelInterface extends ArrayAccess
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
