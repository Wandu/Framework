<?php
namespace Wandu\Database\Connector;

use Interop\Container\ContainerInterface;
use PDO;
use Wandu\Database\Connection\MysqlConnection;
use Wandu\Database\Contracts\ConnectorInterface;

class MysqlConnector implements ConnectorInterface
{
    /** @var string */
    protected $host = 'localhost';
    
    /** @var int */
    protected $port = 4403;
    
    /** @var string */
    protected $username = 'root';
    
    /** @var string */
    protected $password = '';
    
    /** @var string */
    protected $database;
    
    /** @var string */
    protected $charset;
    
    /** @var string */
    protected $collation;
    
    /** @var string */
    protected $prefix = '';
    
    /** @var string */
    protected $timezone;
    
    /** @var array */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        foreach ($settings as $name => $setting) {
            $this->{$name} = $setting;
        }
    }

    /**
     * {@inheritdoc} 
     */
    public function connect(ContainerInterface $container = null)
    {
        $connection = $this->createPdo();
        $this->applyCharset($connection);
        $this->applyTimezone($connection);
        return new MysqlConnection($connection, $container, $this->prefix);
    }

    /**
     * @param \PDO $connection
     */
    protected function applyCharset(PDO $connection)
    {
        if ($this->charset) {
            $names = "SET NAMES '{$this->charset}'";
            if ($this->collation) {
                $names .= " COLLATE '{$this->collation}'";
            }
            $connection->prepare($names)->execute();
        }
    }

    /**
     * @param \PDO $connection
     */
    protected function applyTimezone(PDO $connection)
    {
        if ($this->timezone) {
            $connection->prepare("SET time_zone='{$this->timezone}'")->execute();
        }
    }

    /**
     * @return \PDO
     */
    protected function createPdo()
    {
        return new PDO(
            "mysql:host={$this->host};port={$this->port};dbname={$this->database}",
            $this->username,
            $this->password,
            $this->options + [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
