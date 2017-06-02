<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use ReflectionClass;
use Wandu\Caster\Caster;
use Wandu\Database\Entity\MetadataReader;
use Wandu\DI\Container;
use Wandu\Event\Dispatcher;

class SakilaTestCase extends TestCase
{
    /** @var \Wandu\Database\Manager */
    protected $manager;
    
    /** @var \Wandu\Event\Dispatcher */
    protected $dispatcher;
    
    /** @var \Wandu\Database\Contracts\ConnectionInterface */
    protected $connection;
    
    public function setUp()
    {
        $this->dispatcher = new Dispatcher(new Container());
        $this->manager = new Manager(
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
    
    static function assertEqualsAndSameProperty($expected, $actual)
    {
        if (!is_object($actual) && !is_array($actual)) {
            static::assertSame($expected, $actual);
            return;
        }
        static::assertEquals($expected, $actual);
        if (is_array($expected)) {
            foreach ($expected as $key => $_) {
                static::assertEqualsAndSameProperty($expected[$key], $actual[$key]);
            }
        } else {
            static::assertSame(get_class($expected), get_class($actual));

            $reflClass = new ReflectionClass(get_class($expected));
            $reflProps = $reflClass->getProperties();

            foreach ($reflProps as $reflProp) {
                $reflProp->setAccessible(true);
                static::assertEqualsAndSameProperty($reflProp->getValue($expected), $reflProp->getValue($actual));
            }
        }
    }
}
