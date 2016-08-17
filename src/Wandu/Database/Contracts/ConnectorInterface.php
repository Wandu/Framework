<?php
namespace Wandu\Database\Contracts;

interface ConnectorInterface
{
    /**
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connect();
}
