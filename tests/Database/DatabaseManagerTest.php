<?php
namespace Wandu\Database;

use PHPUnit\Framework\TestCase;
use Wandu\Database\Contracts\Connection;
use Wandu\Database\Exception\DriverNotFoundException;

class DatabaseManagerTest extends TestCase 
{
    public function testConnectFail()
    {
        $manager = new DatabaseManager(new Configuration());
        try {
            $manager->connect([
                'driver' => 'wrong_driver',
                'username' => 'root',
                'password' => '',
                'database' => 'sakila',
            ]);
            static::fail();
        } catch (DriverNotFoundException $e) {
            static::assertEquals(DriverNotFoundException::CODE_UNSUPPORTED, $e->getCode());
            static::assertEquals("\"wrong_driver\" is not supported.", $e->getMessage());
        }
    }

    public function testConnectSuccess()
    {
        $manager = new DatabaseManager(new Configuration());
        
        $connection = $manager->connect([
            'driver' => 'mysql',
            'username' => 'root',
            'password' => '',
            'database' => 'sakila',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'local_',
            'timezone' => '+09:00',
        ]);
        static::assertInstanceOf(Connection::class, $connection);
    }
}
