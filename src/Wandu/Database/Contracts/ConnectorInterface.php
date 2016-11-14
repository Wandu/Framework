<?php
namespace Wandu\Database\Contracts;

use ArrayAccess;

interface ConnectorInterface
{
    /**
     * @param \ArrayAccess $container
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connect(ArrayAccess $container = null);
}
