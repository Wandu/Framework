<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use PDO;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Wandu\Assertions;
use Wandu\Caster\Caster;
use Wandu\Database\Entity\MetadataReader;
use Wandu\DI\Container;
use Wandu\Event\Dispatcher;

class TestCase extends PHPUnitTestCase
{
    use Assertions;
    
    /** @var \Wandu\Database\DatabaseManager */
    protected $manager;
    
    /** @var \Wandu\Event\Dispatcher */
    protected $dispatcher;
    
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;
    
    public function setUp()
    {
        $this->dispatcher = new Dispatcher(new Container());
        $this->manager = new DatabaseManager(
            new MetadataReader(new AnnotationReader()),
            new Caster([
                'datetime' => new Caster\CarbonCaster(),
            ])
        );
        $this->manager->setEventDispatcher($this->dispatcher);
        $this->connection = $this->manager->connect([
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
