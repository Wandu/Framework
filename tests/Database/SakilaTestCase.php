<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PDO;
use PHPUnit_Framework_TestCase;
use Wandu\Database\Connector\MysqlConnector;
use Wandu\DI\Container;

class SakilaTestCase extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;

    public function setUp()
    {
        $container = new Container();
        $container[Reader::class] = new AnnotationReader();
        
        $connector = new MysqlConnector([
            'username' => 'root',
            'password' => 'root',
            'database' => 'sakila',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'timezone' => '+09:00',
            'options' => [ // default
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ]);
        $this->connection = $connector->connect($container);
    }
}
