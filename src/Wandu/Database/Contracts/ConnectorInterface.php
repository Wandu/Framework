<?php
namespace Wandu\Database\Contracts;

use Interop\Container\ContainerInterface;

interface ConnectorInterface
{
    /**
     * @param \Interop\Container\ContainerInterface $container
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connect(ContainerInterface $container = null);
}
