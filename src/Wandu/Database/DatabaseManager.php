<?php
namespace Wandu\Database;

class DatabaseManager
{
    /** @var \Wandu\Database\Connector[] */
    protected $connectors = [];
    
    /** @var \Wandu\Database\Contracts\Connection[] */
    protected $connections = [];
    
    /** @var \Wandu\Database\Repository[] */
    protected $repositories = [];

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }
    
    /**
     * @param array|\Wandu\Database\Connector $connector
     * @param string $name
     * @return \Wandu\Database\Contracts\Connection
     */
    public function connect($connector, $name = 'default')
    {
        if (is_array($connector)) {
            $connector = new Connector($connector);
        }
        $this->connectors[$name] = $connector;
        $connection = $connector->connect();
        if ($emitter = $this->config->getEmitter()) {
            $connection->setEventEmitter($emitter);
        }
        return $this->connections[$name] = $connection;
    }

    /**
     * @param string $name
     * @return \Wandu\Database\Contracts\Connection
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
        if (!isset($this->repositories[$class])) {

            $meta = $this->config->getMetadataReader()->getMetadata($class);
            $connection = $meta->getConnection();
            $prefix = $this->connectors[$connection]->getPrefix();

            $this->repositories[$class] = new Repository(
                $this,
                $this->connections[$connection],
                new QueryBuilder($prefix . $meta->getTable()),
                $meta,
                $this->config
            );
        }
        return $this->repositories[$class];
    }
}
