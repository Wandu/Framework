<?php
namespace Wandu\Database;

use Wandu\Caster\Caster;
use Wandu\Caster\CastManagerInterface;
use Wandu\Database\Connection\MysqlConnection;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Contracts\Entity\MetadataReaderInterface;
use Wandu\Database\Exception\DriverNotFoundException;
use Wandu\Database\Repository;
use Wandu\Event\DispatcherInterface;

class Manager
{
    /** @var \Wandu\Database\Contracts\Entity\MetadataReaderInterface */
    protected $reader;
    
    /** @var \Wandu\Caster\Caster */
    protected $caster;
    
    /** @var \Wandu\Event\DispatcherInterface */
    protected $dispatcher;
    
    /** @var \Wandu\Database\Contracts\ConnectionInterface[] */
    protected $connections = [];
    
    /** @var \Wandu\Database\Repository[] */
    protected $repositories = [];

    public function __construct(
        MetadataReaderInterface $reader,
        CastManagerInterface $caster = null
    ) {
        $this->reader = $reader;
        $this->caster = $caster ?: new Caster();
    }

    /**
     * @param \Wandu\Event\DispatcherInterface $dispatcher
     */
    public function setEventDispatcher(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    /**
     * @param array|\Wandu\Database\Configuration|\Wandu\Database\Contracts\ConnectionInterface $connection
     * @param string $name
     * @return \Wandu\Database\Contracts\ConnectionInterface
     */
    public function connect($connection, $name = 'default')
    {
        if (is_array($connection)) {
            $connection = new Configuration($connection);
        }
        if ($connection instanceof Configuration) {
            switch ($connection->getDriver()) {
                case Configuration::DRIVER_MYSQL:
                    $connection = new MysqlConnection($connection, $this->dispatcher);
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
     * @return \Wandu\Database\Repository
     */
    public function repository(string $class): Repository
    {
        $repoName = "{$class}";
        if (!isset($this->repositories[$repoName])) {
            $this->repositories[$repoName] = new Repository($this, $this->reader->getMetadataFrom($class), $this->caster);
        }
        return $this->repositories[$repoName];
    }
}
