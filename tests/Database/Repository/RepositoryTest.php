<?php
namespace Wandu\Database\Repository;

use Doctrine\Common\Annotations\AnnotationReader;
use InvalidArgumentException;
use stdClass;
use Wandu\Database\Annotations\Column;
use Wandu\Database\Annotations\Table;
use Wandu\Database\Exception\IdentifierNotFoundException;
use Wandu\Database\Query\SelectQuery;
use Wandu\Database\QueryBuilder;
use Wandu\Database\SakilaTestCase;

class RepositoryTest extends SakilaTestCase 
{
    /** @var \Wandu\Database\Repository\Repository */
    protected $repository1;

    /** @var \Wandu\Database\Repository\Repository */
    protected $repository2;

    public function setUp()
    {
        parent::setUp();
        $this->repository1 = new Repository($this->connection, new RepositorySettings('actor', [
            'model' => RepositoryTestActor::class,
            'columns' => [
                'id' => 'actor_id',
                'firstName' => 'first_name',
                'lastName' => 'last_name',
                'lastUpdate' => 'last_update',
            ],
            'casts' => [
                'id' => 'integer',
            ],
            'identifier' => 'id',
            'increments' => true,
        ]));
        $this->repository2 = $this->connection->createRepository(RepositoryTestActor::class);
    }

    public function testFromAnnotation()
    {
        static::assertEquals(new RepositorySettings('actor', [
            'model' => RepositoryTestActor::class,
            'columns' => [
                'id' => 'actor_id',
                'firstName' => 'first_name',
                'lastName' => 'last_name',
                'lastUpdate' => 'last_update',
            ],
            'casts' => [
                'id' => 'integer',
                'firstName' => 'string',
                'lastName' => 'string',
                'lastUpdate' => 'string',
            ],
            'identifier' => 'id',
            'increments' => true,
        ]), RepositorySettings::fromAnnotation(RepositoryTestActor::class, new AnnotationReader()));
    }
    
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
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     */
    public function testFetch($query)
    {
        $expectedModels = [
            new RepositoryTestActor(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            new RepositoryTestActor(181, 'MATTHEW', 'CARREY', '2006-02-15 04:34:33'),
            new RepositoryTestActor(176, 'JON', 'CHASE', '2006-02-15 04:34:33'),
        ];

        $iterateCount = 0;
        foreach ($this->repository1->fetch($query, ["C%"]) as $index => $model) {
            $iterateCount++;
            static::assertNotSame($expectedModels[$index], $model);
            static::assertEquals($expectedModels[$index], $model);
        }
        static::assertEquals(3, $iterateCount);
    }
    
    public function testFetchByQueryBuilder()
    {
        $expectedActor = new RepositoryTestActor(138, 'LUCILLE', 'DEE', '2006-02-15 04:34:33');

        $actor = $this->repository1->first(function (SelectQuery $query) {
            return $query->where('actor_id', 138);
        });
        static::assertEquals($expectedActor, $actor);

        $actors = $this->repository1->fetch(function (SelectQuery $query) {
            return $query->where('actor_id', 138);
        });
        $iterateCount = 0;
        foreach ($actors as $index => $actor) {
            static::assertEquals($expectedActor, $actor);
            $iterateCount++;
        }
        static::assertEquals(1, $iterateCount);
    }

    public function testFind()
    {
        $expectedActor = new RepositoryTestActor(138, 'LUCILLE', 'DEE', '2006-02-15 04:34:33');

        $actor = $this->repository1->find(138);
        static::assertEquals($expectedActor, $actor);

        $actor = $this->repository1->find(-1);
        static::assertNull($actor);
    }

    /**
     * @dataProvider provideSelectQueries
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     */
    public function testFirst($query)
    {
        static::assertEquals(
            new RepositoryTestActor(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            $this->repository1->first($query, ["C%"])
        );
    }

    public function testInsert()
    {
        $repository = $this->repository1;

        try {
            $repository->insert(new stdClass());
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository\\Repository::insert() must be of the type Wandu\\Database\\Repository\\RepositoryTestActor",
                $e->getMessage()
            );
        }
        static::assertEquals(1, $repository->insert($actor = new RepositoryTestActor(null, 'WANDU', 'J', '2016-11-06')));
        static::assertNotNull($actor->getIdentifier());

        static::assertEquals(1, $repository->delete($actor));
        static::assertNull($actor->getIdentifier());

        try {
            $repository->delete($actor);
            static::fail();
        } catch (IdentifierNotFoundException $e) {
            static::assertEquals(
                "Identifier not found from entity",
                $e->getMessage()
            );
        }
    }

    public function testUpdate()
    {
        $repository = $this->repository1;

        try {
            $repository->update(new stdClass());
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository\\Repository::update() must be of the type Wandu\\Database\\Repository\\RepositoryTestActor",
                $e->getMessage()
            );
        }

        /* @var \Wandu\Database\Repository\RepositoryTestActor $actor */
        $actor = $repository->first("SELECT * FROM `actor` WHERE `actor_id` = ?", ['80']);

        static::assertEquals('RALPH', $actor->getFirstName());
        static::assertEquals('CRUZ', $actor->getLastName());

        $actor->setFirstName('CHANGWAN');
        $actor->setLastName('JUN');
        
        static::assertEquals(1, $repository->update($actor));

        /* @var \Wandu\Database\Repository\RepositoryTestActor $actor */
        $actor = $repository->first("SELECT * FROM `actor` WHERE `actor_id` = ?", ['80']);

        static::assertEquals('CHANGWAN', $actor->getFirstName());
        static::assertEquals('JUN', $actor->getLastName());

        $actor->setFirstName('RALPH');
        $actor->setLastName('CRUZ');

        static::assertEquals(1, $repository->update($actor));
    }
}

/**
 * @Table(name="actor", identifier="id", increments=true)
 */
class RepositoryTestActor
{
    /**
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
