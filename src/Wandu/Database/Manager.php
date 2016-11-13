<?php
namespace Wandu\Database;

use Interop\Container\ContainerInterface;
use Wandu\Database\Connector\MysqlConnector;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Contracts\ConnectorInterface;
use Wandu\Database\Exception\DriverNotFoundException;

class Manager
{
    /** @var \Wandu\Database\Manager */
    protected static $instance;

    /** @var \Interop\Container\ContainerInterface */
    protected $container;

    /** @var array */
    protected $connections = [];

    /**
     * @param \Interop\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
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
        return $this->connection($information->connect($this->container), $name);
    }

    /**
     * @param \Wandu\Database\Contracts\ConnectionInterface $connection
     * @param string $name
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connection(ConnectionInterface $connection, $name = 'default')
    {
        return $this->connections[$name] = $connection;
    }

    /**
     * @return \Wandu\Database\Manager
     */
    public function setAsGlobal()
    {
        $beforeInstance = static::$instance;
        static::$instance = $this;
        return $beforeInstance;
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
