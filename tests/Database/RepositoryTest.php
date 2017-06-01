<?php
namespace Wandu\Database;

use InvalidArgumentException;
use stdClass;
use Wandu\Database\Query\SelectQuery;
use Wandu\Database\Sakila\SakilaActor;

class RepositoryTest extends SakilaTestCase 
{
    /** @var \Wandu\Database\Repository */
    protected $repository;
    
    public function setUp()
    {
        parent::setUp();
        $this->repository = $this->manager->repository(SakilaActor::class);
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
            new SakilaActor(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            new SakilaActor(181, 'MATTHEW', 'CARREY', '2006-02-15 04:34:33'),
            new SakilaActor(176, 'JON', 'CHASE', '2006-02-15 04:34:33'),
        ];

        $iterateCount = 0;
        foreach ($this->repository->fetch($query, ["C%"]) as $index => $model) {
            $iterateCount++;
            static::assertNotSame($expectedModels[$index], $model);
            static::assertEquals($expectedModels[$index], $model);
        }
        static::assertEquals(3, $iterateCount);
    }
    
    public function testFetchByQueryBuilder()
    {
        $expectedActor = new SakilaActor(138, 'LUCILLE', 'DEE', '2006-02-15 04:34:33');

        $actor = $this->repository->first(function (SelectQuery $query) {
            return $query->where('actor_id', 138);
        });
        static::assertEquals($expectedActor, $actor);

        $actors = $this->repository->fetch(function (SelectQuery $query) {
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
        $expectedActor = new SakilaActor(138, 'LUCILLE', 'DEE', '2006-02-15 04:34:33');

        $actor = $this->repository->find(138);
        static::assertEquals($expectedActor, $actor);

        $actor = $this->repository->find(-1);
        static::assertNull($actor);
    }

    /**
     * @dataProvider provideSelectQueries
     * @param string|callable|\Wandu\Database\Contracts\QueryInterface $query
     */
    public function testFirst($query)
    {
        static::assertEquals(
            new SakilaActor(183, 'RUSSELL', 'CLOSE', '2006-02-15 04:34:33'),
            $this->repository->first($query, ["C%"])
        );
    }

    public function testInsert()
    {
        try {
            $this->repository->persist(new stdClass());
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository::persist() must be of the type " . SakilaActor::class,
                $e->getMessage()
            );
        }
        static::assertEquals(1, $this->repository->persist($actor = new SakilaActor(null, 'WANDU', 'J', '2016-11-06')));
        static::assertNotNull($actor->getIdentifier());

        static::assertEquals(1, $this->repository->delete($actor));
        static::assertNull($actor->getIdentifier());

        static::assertEquals(0, $this->repository->delete($actor));
    }

    public function testUpdate()
    {
        $repository = $this->repository;

        try {
            $repository->persist(new stdClass());
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository::persist() must be of the type " . SakilaActor::class,
                $e->getMessage()
            );
        }

        /* @var \Wandu\Database\Sakila\SakilaActor $actor */
        $actor = $repository->first("SELECT * FROM `actor` WHERE `actor_id` = ?", ['80']);

        static::assertEquals('RALPH', $actor->getFirstName());
        static::assertEquals('CRUZ', $actor->getLastName());

        $actor->setFirstName('CHANGWAN');
        $actor->setLastName('JUN');
        
        static::assertEquals(1, $repository->persist($actor));

        /* @var \Wandu\Database\Sakila\SakilaActor $actor */
        $actor = $repository->first("SELECT * FROM `actor` WHERE `actor_id` = ?", ['80']);

        static::assertEquals('CHANGWAN', $actor->getFirstName());
        static::assertEquals('JUN', $actor->getLastName());

        $actor->setFirstName('RALPH');
        $actor->setLastName('CRUZ');

        static::assertEquals(1, $repository->persist($actor));
    }
}
