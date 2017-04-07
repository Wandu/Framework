<?php
namespace Wandu\Database;

use PDO;
use PHPUnit_Framework_TestCase;

class SakilaTestCase extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;
    
    public function setUp()
    {
        $manager = new Manager();
        $this->connection = $manager->connect([
            'username' => 'root',
            'password' => '',
            'database' => 'sakila',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => [ // default
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ]);
    }
}
