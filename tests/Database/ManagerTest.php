<?php
namespace Wandu\Database;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Database\Contracts\ConnectionInterface;
use Wandu\Database\Contracts\Entity\MetadataReaderInterface;
use Wandu\Database\Exception\DriverNotFoundException;

class ManagerTest extends PHPUnit_Framework_TestCase
{
    public function testConnectFail()
    {
        $manager = new Manager(Mockery::mock(MetadataReaderInterface::class));
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
        $manager = new Manager(Mockery::mock(MetadataReaderInterface::class));
        
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
        static::assertInstanceOf(ConnectionInterface::class, $connection);
    }
}
