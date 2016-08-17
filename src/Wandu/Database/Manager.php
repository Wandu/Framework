<?php
namespace Wandu\Database;

use Wandu\Database\Contracts\ConnectorInterface;

class Manager
{
    /** @var array */
    protected $connectors = [];
    
    public function connect(ConnectorInterface $connect, $name = 'default')
    {
        $this->connectors[$name] = $connect->connect();
    }
}
