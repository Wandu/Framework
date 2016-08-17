<?php
namespace Wandu\Database;

use PHPUnit_Framework_TestCase;
use Wandu\Database\Connector\MysqlConnector;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
    public function testConnect()
    {
        $connector = new MysqlConnector([
            'username' => 'root',
            'password' => 'root',
            'database' => 'allbus',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'local_',
            // 'timezone' => '+09:00',
        ]);
        
        $connection = $connector->connect();

        print_r($connection->createQueryBuilder('users'));
        
    }
}
