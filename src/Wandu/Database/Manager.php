<?php
namespace Wandu\Database;

use ArrayAccess;
use Wandu\Database\Connector\MysqlConnector;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Contracts\ConnectorInterface;
use Wandu\Database\Exception\DriverNotFoundException;

class Manager
{
    /** @var \ArrayAccess */
    protected $container;

    /** @var \Wandu\Database\Contracts\ConnectionInterface[] */
    protected $connections = [];

    /**
     * @param \ArrayAccess $container
     */
    public function __construct(ArrayAccess $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param array|\Wandu\Database\Contracts\ConnectorInterface $information
     * @param string $name
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connect($information, $name = 'default')
    {
        if (!$information instanceof ConnectorInterface) {
            $information = $this->getConnectorFromConfig($information);
        }
        return $this->setConnection($information->connect($this->container), $name);
    }

    /**
     * @param \Wandu\Database\Contracts\ConnectionInterface $connection
     * @param string $name
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function setConnection(ConnectionInterface $connection, $name = 'default')
    {
        return $this->connections[$name] = $connection;
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function getConnection($name = 'defeault')
    {
        return isset($this->connections[$name]) ? $this->connections[$name] : null;
    }

    /**
     * @param array $config
     * @return \Wandu\Database\Contracts\ConnectorInterface
     */
    private function getConnectorFromConfig(array $config)
    {
        if (!isset($config['driver'])) {
            throw new DriverNotFoundException();
        }
        switch ($config['driver']) {
            case 'mysql':
                return new MysqlConnector($config);
        }
        throw new DriverNotFoundException($config['driver']);
    }
}
