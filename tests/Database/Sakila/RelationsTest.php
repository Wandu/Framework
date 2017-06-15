<?php
namespace Wandu\Database\Sakila;

use Carbon\Carbon;
use Wandu\Collection\ArrayList;
use Wandu\Database\Events\ExecuteQuery;
use Wandu\Database\Sakila\Models\SakilaCity;
use Wandu\Database\Sakila\Models\SakilaCountry;
use Wandu\Database\Sakila\Models\SakilaFilm;

class RelationsTest extends TestCase
{
    public function testHasOne()
    {
        $this->emitter->on(ExecuteQuery::class, function (ExecuteQuery $e) {
            echo $e->getSql(), " | ";
            echo "[", implode(', ', $e->getBindings()), "]\n";
        });
        
        $expected = <<<OB
SELECT * FROM `film` WHERE `film_id` = ? | [1]
SELECT * FROM `language` WHERE `language_id` = ? | [1]

OB;
        /** @var \Wandu\Database\Sakila\Models\SakilaFilm $film */
        $film = static::assertOutputBufferEquals($expected, function () {
            $filmRepo = $this->manager->repository(SakilaFilm::class);
            return $filmRepo->find(1);
        });

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
        $this->emitter->on(ExecuteQuery::class, function (ExecuteQuery $e) {
            echo $e->getSql(), " | ";
            echo "[", implode(', ', $e->getBindings()), "]\n";
        });

        $expected = <<<OB
SELECT * FROM `film` WHERE `film_id` IN (?, ?) | [1, 2]
SELECT * FROM `language` WHERE `language_id` = ? | [1]
SELECT * FROM `language` WHERE `language_id` = ? | [1]

OB;
        /** @var \Wandu\Database\Sakila\Models\SakilaLanguage[] $languages */
        $languages = static::assertOutputBufferEquals($expected, function () {
            $filmRepo = $this->manager->repository(SakilaFilm::class);
            return $filmRepo->findMany([1, 2])->map(function (SakilaFilm $film) {
                return $film->getLanguage();
            });
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
        $this->emitter->on(ExecuteQuery::class, function (ExecuteQuery $e) {
            echo $e->getSql(), " | ";
            echo "[", implode(', ', $e->getBindings()), "]\n";
        });

        $expected = <<<OB
SELECT * FROM `country` WHERE `country_id` = ? | [2]
SELECT * FROM `city` WHERE `country_id` = ? | [2]
SELECT * FROM `country` WHERE `country_id` = ? | [2]
SELECT * FROM `country` WHERE `country_id` = ? | [2]
SELECT * FROM `country` WHERE `country_id` = ? | [2]

OB;
        /** @var \Wandu\Database\Sakila\Models\SakilaCountry $actual */
        $actual = static::assertOutputBufferEquals($expected, function () {
            $countryRepo = $this->manager->repository(SakilaCountry::class);
            return $countryRepo->find(2);
        });
        
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
