<?php
namespace Wandu\Modelr;

use Wandu\Modelr\Contracts\ConnectorInterface;

class Manager
{
    /** @var array */
    protected $connectors = [];
    
    public function connect(ConnectorInterface $connect, $name = 'default')
    {
        $this->connectors[$name] = $connect->connect();
    }
}
