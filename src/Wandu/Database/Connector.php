<?php
namespace Wandu\Database;

use PDO;
use Wandu\Database\Connection\MysqlConnection;
use Wandu\Database\Contracts\Connection;
use Wandu\Database\Exception\DriverNotFoundException;

class Connector
{
    const DRIVER_MYSQL = 'mysql';
    
    /** @var string */
    protected $driver = 'mysql';
    
    /** @var string */
    protected $host = 'localhost';

    /** @var int */
    protected $port = 4403;

    /** @var string */
    protected $username = 'root';

    /** @var string */
    protected $password = '';

    /** @var string */
    protected $database = null;

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
     * @return \Wandu\Database\Contracts\Connection
     */
    public function connect(): Connection
    {
        switch ($this->driver) {
            case Connector::DRIVER_MYSQL:
                return new MysqlConnection($this->createPdo());
        }
        throw new DriverNotFoundException($this->driver);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
    
    /**
     * @return \PDO
     */
    public function createPdo()
    {
        $pdo = new PDO(
            "{$this->driver}:host={$this->host};port={$this->port};dbname={$this->database}",
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
        $this->applyCharset($pdo);
        $this->applyTimezone($pdo);
        return $pdo;
    }

    /**
     * @param \PDO $pdo
     */
    protected function applyCharset(PDO $pdo)
    {
        if ($this->charset) {
            $names = "SET NAMES '{$this->charset}'";
            if ($this->collation) {
                $names .= " COLLATE '{$this->collation}'";
            }
            $pdo->prepare($names)->execute();
        }
    }

    /**
     * @param \PDO $pdo
     */
    protected function applyTimezone(PDO $pdo)
    {
        if ($this->timezone) {
            $pdo->prepare("SET time_zone='{$this->timezone}'")->execute();
        }
    }
}
