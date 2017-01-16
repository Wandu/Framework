<?php
namespace Wandu\Database;

use Wandu\Database\Connection\MysqlConnection;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Exception\DriverNotFoundException;

class Manager
{
    /** @var \Wandu\Database\Contracts\ConnectionInterface[] */
    protected $connections = [];

    /**
     * @param array|\Wandu\Database\Configuration|\Wandu\Database\Contracts\ConnectionInterface $connection
     * @param string $name
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connect($connection, $name = 'default')
    {
        if (!$connection instanceof Configuration) {
            $connection = new Configuration($connection);
        }
        if (!$connection instanceof ConnectionInterface) {
            switch ($connection->getDriver()) {
                case Configuration::DRIVER_MYSQL:
                    $connection = new MysqlConnection($connection);
                    break;
                default:
                    throw new DriverNotFoundException($connection->getDriver());
            }
        }
        $connection->connect();
        return $this->connections[$name] = $connection;
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connection($name = 'default')
    {
        return isset($this->connections[$name]) ? $this->connections[$name] : null;
    }
}
