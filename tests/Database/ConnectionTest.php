<?php
namespace Wandu\Database;

use PDO;
use PHPUnit_Framework_TestCase;
use Wandu\Database\Connector\MysqlConnector;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Query\QueryBuilder;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
    public function testConnect()
    {
        $connector = new MysqlConnector([
            'username' => 'root',
            'password' => 'root',
            'database' => 'wandu',
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
        
        $connection = $connector->connect();

        $cursor = $connection->fetch(function (ConnectionInterface $connection) {
            return $connection->createQueryBuilder('users')->orderBy('id', false);
        });
        
        foreach ($cursor as $row) {
            print_r($row);
        }
        
    }
}
