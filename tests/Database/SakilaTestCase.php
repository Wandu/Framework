<?php
namespace Wandu\Database;

use PDO;
use PHPUnit_Framework_TestCase;
use Wandu\Database\Connector\MysqlConnector;

class SakilaTestCase extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;

    public function setUp()
    {
        $connector = new MysqlConnector([
            'username' => 'root',
            'password' => 'root',
            'database' => 'sakila',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'local_',
            'timezone' => '+09:00',
            'options' => [ // default
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ]);
        $this->connection = $connector->connect();
    }
}
