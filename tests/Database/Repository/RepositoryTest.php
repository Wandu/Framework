<?php
namespace Wandu\Database\Repository;

use Doctrine\Common\Annotations\AnnotationReader;
use Wandu\Database\Exception\IdentifierNotFoundException;
use Wandu\Database\Query\SelectQuery;
use Wandu\Database\QueryBuilder;
use Wandu\Database\Sakila\SakilaActor;
use Wandu\Database\SakilaTestCase;
use InvalidArgumentException;
use stdClass;

class RepositoryTest extends SakilaTestCase 
{
    /** @var \Wandu\Database\Repository\Repository */
    protected $repository;
    
    public function setUp()
    {
        parent::setUp();
        $settings = RepositorySettings::fromAnnotation(SakilaActor::class, new AnnotationReader());
        $this->repository = new Repository($this->connection, $settings);
    }
    
    public function testFromAnnotation()
    {
        static::assertEquals(new RepositorySettings('actor', [
            'model' => SakilaActor::class,
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
        ]), RepositorySettings::fromAnnotation(SakilaActor::class, new AnnotationReader()));
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
            $this->repository->insert(new stdClass());
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository\\Repository::insert() must be of the type " . SakilaActor::class,
                $e->getMessage()
            );
        }
        static::assertEquals(1, $this->repository->insert($actor = new SakilaActor(null, 'WANDU', 'J', '2016-11-06')));
        static::assertNotNull($actor->getIdentifier());

        static::assertEquals(1, $this->repository->delete($actor));
        static::assertNull($actor->getIdentifier());

        try {
            $this->repository->delete($actor);
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
        $repository = $this->repository;

        try {
            $repository->update(new stdClass());
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                "Argument 1 passed to Wandu\\Database\\Repository\\Repository::update() must be of the type " . SakilaActor::class,
                $e->getMessage()
            );
        }

        /* @var \Wandu\Database\Sakila\SakilaActor $actor */
        $actor = $repository->first("SELECT * FROM `actor` WHERE `actor_id` = ?", ['80']);

        static::assertEquals('RALPH', $actor->getFirstName());
        static::assertEquals('CRUZ', $actor->getLastName());

        $actor->setFirstName('CHANGWAN');
        $actor->setLastName('JUN');
        
        static::assertEquals(1, $repository->update($actor));

        /* @var \Wandu\Database\Sakila\SakilaActor $actor */
        $actor = $repository->first("SELECT * FROM `actor` WHERE `actor_id` = ?", ['80']);

        static::assertEquals('CHANGWAN', $actor->getFirstName());
        static::assertEquals('JUN', $actor->getLastName());

        $actor->setFirstName('RALPH');
        $actor->setLastName('CRUZ');

        static::assertEquals(1, $repository->update($actor));
    }
}
