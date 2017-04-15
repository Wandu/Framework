<?php
namespace Wandu\Database;

use Carbon\Carbon;
use Illuminate\Database\Events\QueryExecuted;
use Wandu\Collection\ArrayList;
use Wandu\Database\Events\ExecuteQuery;
use Wandu\Database\Query\SelectQuery;
use Wandu\Database\Sakila\SakilaCity;
use Wandu\Database\Sakila\SakilaCountry;
use Wandu\Database\Sakila\SakilaFilm;
use Wandu\Database\Sakila\SakilaLanguage;
use Wandu\Event\Listener;
use Mockery;

class OrmRelationTest extends SakilaTestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testHasOne()
    {
        $listener = Mockery::mock(Listener::class);
        $listener->shouldReceive('call')->with(Mockery::type(ExecuteQuery::class))->times(2);

        $this->dispatcher->on(ExecuteQuery::class, $listener);

        $filmRepo = $this->manager->repository(SakilaFilm::class);

        static::assertEqualsAndSameProperty(
            new SakilaFilm(
                1,
                'ACADEMY DINOSAUR',
                'A Epic Drama of a Feminist And a Mad Scientist who must Battle a Teacher in The Canadian Rockies',
                2006,
                new SakilaLanguage(1, 'English', new Carbon('2006-02-15 05:02:19'))
            ),
            $filmRepo->find(1)
        );
    }

    public function testHasOneByAll()
    {
        $listener = Mockery::mock(Listener::class);
        $listener->shouldReceive('call')->with(Mockery::type(ExecuteQuery::class))->times(3); // @todo it will be 2

        $this->dispatcher->on(ExecuteQuery::class, $listener);

        $filmRepo = $this->manager->repository(SakilaFilm::class);

        static::assertEqualsAndSameProperty(
            [
                new SakilaLanguage(1, 'English', new Carbon('2006-02-15 05:02:19')),
                new SakilaLanguage(1, 'English', new Carbon('2006-02-15 05:02:19')),
            ],
            $filmRepo->findMany([1, 2])->map(function (SakilaFilm $film) {
                return $film->getLanguage();
            })->toArray()
        );
    }

    public function testHasManyAndCircular()
    {
        $listener = Mockery::mock(Listener::class);
        $listener->shouldReceive('call')->with(Mockery::type(ExecuteQuery::class))->times(5); // -_ㅠ..

        $this->dispatcher->on(ExecuteQuery::class, $listener);

        $countryRepo = $this->manager->repository(SakilaCountry::class);
        
        /** @var \Wandu\Database\Sakila\SakilaCountry $actual */
        $actual = $countryRepo->find(2);
        static::assertSame(2, $actual->getId());
        static::assertSame('Algeria', $actual->getName());
        static::assertEquals(new Carbon('2006-02-15 04:44:00'), $actual->getLastUpdate());
        
        $actualCities = $actual->getCities();
        static::assertEquals(
            [59, 63, 483],
            $actualCities->map(function (SakilaCity $city) { return $city->getId(); })->toArray()
        );
        static::assertEquals(
            ['Batna', 'Bchar', 'Skikda'],
            $actualCities->map(function (SakilaCity $city) { return $city->getName(); })->toArray()
        );
        static::assertSame(
            $actual,
            $actualCities[0]->getCountry()
        );
        static::assertSame(
            $actual,
            $actualCities[1]->getCountry()
        );
        static::assertSame(
            $actual,
            $actualCities[2]->getCountry()
        );
    }
}
