<?php
namespace Wandu\Database\Sakila;

use Doctrine\Common\Annotations\AnnotationReader;
use Mockery;
use PDO;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Wandu\Assertions;
use Wandu\Caster\Caster;
use Wandu\Database\Configuration;
use Wandu\Database\DatabaseManager;
use Wandu\Database\Entity\MetadataReader;
use Wandu\DI\Container;
use Wandu\Event\EventEmitter;

class TestCase extends PHPUnitTestCase
{
    use Assertions;
    
    /** @var \Wandu\Database\DatabaseManager */
    protected $manager;
    
    /** @var \Wandu\Event\Contracts\EventEmitter */
    protected $emitter;
    
    /** @var \Wandu\Database\Contracts\Connection */
    protected $connection;
    
    public function setUp()
    {
        $this->emitter = new EventEmitter();
        $this->emitter->setContainer(new Container());
     
        $config = new Configuration(
            null,
            new Caster([
                'datetime' => new Caster\CarbonCaster(),
            ]),
            $this->emitter
        );
        $this->manager = new DatabaseManager($config);
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

    public function tearDown()
    {
        Mockery::close();
    }
}
