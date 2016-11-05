<?php
namespace Wandu\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use InvalidArgumentException;
use stdClass;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\GenerateOnInsert;
use Wandu\Database\Annotations\Identifier;
use Wandu\Database\Annotations\Table;

class RepositoryTest extends SakilaTestCase
{
    public function provideSelectQueries()
    {
        return [
            ["SELECT * FROM `actor` WHERE `last_name` LIKE ? ORDER BY `actor_id` DESC LIMIT 3"],
            [function () {
                return "SELECT * FROM `actor` WHERE `last_name` LIKE ? ORDER BY `actor_id` DESC LIMIT 3";
            }],
            [(new QueryBuilder('actor'))->select()->where('last_name', 'LIKE', 'C%')->orderBy('actor_id', false)->take(3)],
            [function () {
                return (new QueryBuilder('actor'))->select()->where('last_name', 'LIKE', 'C%')->orderBy('actor_id', false)->take(3);
            }],
        ];
    }

    /**
     * @dataProvider provideSelectQueries
     */
    public function testFetch($query)
    {
        $expectedModels = [
            new Actor(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            new Actor(181, 'MATTHEW', 'CARREY', '2006-02-15 04:34:33'),
            new Actor(176, 'JON', 'CHASE', '2006-02-15 04:34:33'),
        ];
        
        $repository = new Repository($this->connection, new AnnotationReader(), Actor::class);
        $iterateCount = 0;
        foreach ($repository->fetch($query, ["C%"]) as $index => $model) {
            $iterateCount++;
            static::assertNotSame($expectedModels[$index], $model);
            static::assertEquals($expectedModels[$index], $model);
        }
        static::assertEquals(3, $iterateCount);
    }

    /**
     * @dataProvider provideSelectQueries
     */
    public function testFirst($query)
    {
        $repository = new Repository($this->connection, new AnnotationReader(), Actor::class);
        static::assertEquals(
            new Actor(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            $repository->first($query, ["C%"])
        );
    }
    
    public function testStore()
    {
        $repository = new Repository($this->connection, new AnnotationReader(), Actor::class);

        try {
            $repository->store(new stdClass(), 'actor');
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository::store() must be of the type Wandu\\Database\\Actor",
                $e->getMessage()
            );
        }
        $repository->store($actor = new Actor(null, 'WANDU', 'J', '2016-11-06'), 'actor');
        static::assertNotNull($actor->getId());
    }
    /*
     * public function all($columns = array('*'))
        public function lists($value, $key = null)
        public function paginate($perPage = 1, $columns = array('*'));
        public function create(array $data)
        // if you use mongodb then you'll need to specify primary key $attribute
        public function update(array $data, $id, $attribute = "id")
        public function delete($id)
        public function find($id, $columns = array('*'))
        public function findBy($field, $value, $columns = array('*'))
        public function findAllBy($field, $value, $columns = array('*'))
        public function findWhere($where, $columns = array('*'))
     */
}

/**
 * @Table(name="actor")
 */
class Actor
{
    /**
     * @Identifier
     * @GenerateOnInsert
     * @Column(name="actor_id", cast="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(name="first_name")
     * @var string
     */
    private $firstName;

    /**
     * @Column(name="last_name")
     * @var string
     */
    private $lastName;

    /**
     * @Column(name="last_update")
     * @var string
     */
    private $lastUpdate;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $lastUpdate
     */
    public function __construct($id, $firstName, $lastName, $lastUpdate)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
