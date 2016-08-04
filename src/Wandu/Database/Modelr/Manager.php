<?php
namespace Wandu\Database\Modelr;

use Wandu\Database\Modelr\Contracts\ConnectorInterface;

class Manager
{
    /** @var array */
    protected $connectors = [];
    
    public function connect(ConnectorInterface $connect, $name = 'default')
    {
        $this->connectors[$name] = $connect->connect();
    }
}
