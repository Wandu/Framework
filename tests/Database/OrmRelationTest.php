<?php
namespace Wandu\Database;

use Carbon\Carbon;
use Mockery;
use Wandu\Collection\ArrayList;
use Wandu\Database\Events\ExecuteQuery;
use Wandu\Database\Sakila\SakilaCity;
use Wandu\Database\Sakila\SakilaCountry;
use Wandu\Database\Sakila\SakilaFilm;
use Wandu\Event\Listener;

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

        /** @var \Wandu\Database\Sakila\SakilaFilm $film */
        $film = $filmRepo->find(1);

        static::assertSame(1, $film->getId());
        static::assertSame('ACADEMY DINOSAUR', $film->getTitle());
        static::assertSame('A Epic Drama of a Feminist And a Mad Scientist who must Battle a Teacher in The Canadian Rockies', $film->getDescription());
        static::assertSame(2006, $film->getReleaseYear());

        static::assertSame(1, $film->getLanguage()->getId());
        static::assertSame('English', $film->getLanguage()->getName());
        static::assertEquals(new Carbon('2006-02-15 05:02:19'), $film->getLanguage()->getLastUpdate());
    }

    public function testHasOneByAll()
    {
        $listener = Mockery::mock(Listener::class);
        $listener->shouldReceive('call')->with(Mockery::type(ExecuteQuery::class))->times(3); // @todo it will be 2

        $this->dispatcher->on(ExecuteQuery::class, $listener);

        $filmRepo = $this->manager->repository(SakilaFilm::class);


        /** @var \Wandu\Database\Sakila\SakilaLanguage[] $languages */
        $languages = $filmRepo->findMany([1, 2])->map(function (SakilaFilm $film) {
            return $film->getLanguage();
        });

        static::assertInstanceOf(ArrayList::class, $languages);
        
        static::assertSame(1, $languages[0]->getId());
        static::assertSame('English', $languages[0]->getName());
        static::assertEquals(new Carbon('2006-02-15 05:02:19'), $languages[0]->getLastUpdate());

        static::assertSame(1, $languages[1]->getId());
        static::assertSame('English', $languages[1]->getName());
        static::assertEquals(new Carbon('2006-02-15 05:02:19'), $languages[1]->getLastUpdate());
    }

    public function testHasManyAndCircular()
    {
        $listener = Mockery::mock(Listener::class);
        $listener->shouldReceive('call')->with(Mockery::type(ExecuteQuery::class))->times(5); // -_ㅠ.. @todo it will be 2

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
