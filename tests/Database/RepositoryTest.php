<?php
namespace Wandu\Database;

use InvalidArgumentException;
use stdClass;
use Wandu\Database\Repository\RepositorySettings;

/**
 * @todo
 * all($columns = array('*'))
 * lists($value, $key = null)
 * paginate($perPage = 1, $columns = array('*'));
 * create(array $data)
 * update(array $data, $id, $attribute = "id")
 * delete($id)
 * find($id, $columns = array('*'))
 * findBy($field, $value, $columns = array('*'))
 * findAllBy($field, $value, $columns = array('*'))
 * findWhere($where, $columns = array('*'))
 */
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
            new RepositoryTestActorModel(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            new RepositoryTestActorModel(181, 'MATTHEW', 'CARREY', '2006-02-15 04:34:33'),
            new RepositoryTestActorModel(176, 'JON', 'CHASE', '2006-02-15 04:34:33'),
        ];

        $repository = new Repository($this->connection, new RepositorySettings('actor', [
            'model' => RepositoryTestActorModel::class,
            'columns' => [
                'actor_id' => 'id',
                'first_name' => 'firstName',
                'last_name' => 'lastName',
                'last_update' => 'lastUpdate',
            ],
            'casts' => [
                'actor_id' => 'integer',
            ],
            'identifier' => 'actor_id',
            'increments' => true,
        ]));
        
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
        $repository = new Repository($this->connection, new RepositorySettings('actor', [
            'model' => RepositoryTestActorModel::class,
            'columns' => [
                'actor_id' => 'id',
                'first_name' => 'firstName',
                'last_name' => 'lastName',
                'last_update' => 'lastUpdate',
            ],
            'casts' => [
                'actor_id' => 'integer',
            ],
            'identifier' => 'actor_id',
            'increments' => true,
        ]));
        static::assertEquals(
            new RepositoryTestActorModel(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            $repository->first($query, ["C%"])
        );
    }

    public function testInsert()
    {
        $repository = new Repository($this->connection, new RepositorySettings('actor', [
            'model' => RepositoryTestActorModel::class,
            'columns' => [
                'actor_id' => 'id',
                'first_name' => 'firstName',
                'last_name' => 'lastName',
                'last_update' => 'lastUpdate',
            ],
            'casts' => [
                'actor_id' => 'integer',
            ],
            'identifier' => 'actor_id',
            'increments' => true,
        ]));

        try {
            $repository->insert(new stdClass());
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository::insert() must be of the type Wandu\\Database\\RepositoryTestActorModel",
                $e->getMessage()
            );
        }
        static::assertEquals(1, $repository->insert($actor = new RepositoryTestActorModel(null, 'WANDU', 'J', '2016-11-06')));
        static::assertNotNull($actor->getIdentifier());

        static::assertEquals(1, $repository->delete($actor));
        static::assertNull($actor->getIdentifier());

        try {
            $repository->delete($actor);
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Cannot get the identifier from the entity",
                $e->getMessage()
            );
        }
    }

    public function testUpdate()
    {
        $repository = new Repository($this->connection, new RepositorySettings('actor', [
            'model' => RepositoryTestActorModel::class,
            'columns' => [
                'actor_id' => 'id',
                'first_name' => 'firstName',
                'last_name' => 'lastName',
                'last_update' => 'lastUpdate',
            ],
            'casts' => [
                'actor_id' => 'integer',
            ],
            'identifier' => 'actor_id',
            'increments' => true,
        ]));

        try {
            $repository->update(new stdClass());
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository::update() must be of the type Wandu\\Database\\RepositoryTestActorModel",
                $e->getMessage()
            );
        }
        
        /** @var \Wandu\Database\RepositoryTestActorModel $actor */
        $actor = $repository->first("SELECT * FROM `actor` WHERE `actor_id` = ?", ['80']);

        static::assertEquals('RALPH', $actor->getFirstName());
        static::assertEquals('CRUZ', $actor->getLastName());

        $actor->setFirstName('CHANGWAN');
        $actor->setLastName('JUN');
        
        static::assertEquals(1, $repository->update($actor));

        $actor = $repository->first("SELECT * FROM `actor` WHERE `actor_id` = ?", ['80']);

        static::assertEquals('CHANGWAN', $actor->getFirstName());
        static::assertEquals('JUN', $actor->getLastName());

        $actor->setFirstName('RALPH');
        $actor->setLastName('CRUZ');

        static::assertEquals(1, $repository->update($actor));
    }
}

class RepositoryTestActorModel
{
    /** @var int */
    private $id;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
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
    
    public function getIdentifier()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
}
