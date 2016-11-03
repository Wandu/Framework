<?php
namespace Wandu\Database\Connector;

use PDO;
use Wandu\Database\Connection\MysqlConnection;
use Wandu\Database\Contracts\ConnectorInterface;

class MysqlConnector implements ConnectorInterface
{
    /** @var array */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc} 
     */
    public function connect()
    {
        $connection = $this->createPdo(isset($this->config['options']) ? $this->config['options'] : []);

        $this->applyCharset($connection);
        $this->applyTimezone($connection);
        
        return new MysqlConnection($connection, [
            'prefix' => isset($this->config['prefix']) ? $this->config['prefix'] : '',
        ]);
    }

    /**
     * @param \PDO $connection
     */
    protected function applyCharset(PDO $connection)
    {
        if (isset($this->config['charset'])) {
            $charset = $this->config['charset'];
            $collation = isset($this->config['collation']) ? $this->config['collation'] : null;
            $names = "SET NAMES '{$charset}'";
            if ($collation) {
                $names .= " COLLATE '{$collation}'";
            }
            $connection->prepare($names)->execute();
        }
    }

    /**
     * @param \PDO $connection
     */
    protected function applyTimezone(PDO $connection)
    {
        if (isset($this->config['timezone'])) {
            $timezone = $this->config['timezone'];
            $connection->prepare("SET time_zone='{$timezone}'")->execute();
        }
    }

    /**
     * @param array $options
     * @return \PDO
     */
    protected function createPdo(array $options = [])
    {
        $username = isset($this->config['username']) ? $this->config['username'] : 'root';
        $password = isset($this->config['password']) ? $this->config['password'] : '';

        $host = isset($this->config['host']) ? $this->config['host'] : 'localhost';
        $port = isset($this->config['port']) ? $this->config['port'] : 3306;

        $database = $this->config['database'];
        
        return new PDO(
            "mysql:host={$host};port={$port};dbname={$database}",
            $username,
            $password,
            $options + [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
