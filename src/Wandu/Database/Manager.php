<?php
namespace Wandu\Database;

use Wandu\Database\Connection\MysqlConnection;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Contracts\Entity\MetadataReaderInterface;
use Wandu\Database\Exception\DriverNotFoundException;
use Wandu\Database\Repository\Repository;

class Manager
{
    /** @var \Wandu\Database\Contracts\Entity\MetadataReaderInterface */
    protected $reader;
    
    /** @var \Wandu\Database\Contracts\ConnectionInterface[] */
    protected $connections = [];
    
    /** @var \Wandu\Database\Repository\Repository[] */
    protected $repositories = [];

    public function __construct(MetadataReaderInterface $reader)
    {
        $this->reader = $reader;
    }

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

    /**
     * @param string $class
     * @param string $connection
     * @return \Wandu\Database\Repository\Repository
     */
    public function repository(string $class, string $connection = 'default'): Repository
    {
        $repoName = "{$class}@{$connection}";
        if (!isset($this->repositories[$repoName])) {
            $this->repositories[$repoName] = new Repository($this, $this->reader->getMetadataFrom($class));
        }
        return $this->repositories[$repoName];
    }
}
