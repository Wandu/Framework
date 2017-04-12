<?php
namespace Wandu\Database;

use Wandu\Database\Sakila\SakilaFilm;
use Wandu\Database\Sakila\SakilaLanguage;

class OrmTest extends SakilaTestCase
{
    public function testLanguage()
    {
        $languageRepo = $this->manager->repository(SakilaLanguage::class);
        
        static::assertEquals(
            new SakilaLanguage(1, 'English', '2006-02-15 05:02:19'),
            $languageRepo->find(1)
        );
    }

    public function testHasOne()
    {
        $filmRepo = $this->manager->repository(SakilaFilm::class);

        static::assertEquals(
            new SakilaFilm(
                1,
                'ACADEMY DINOSAUR',
                'A Epic Drama of a Feminist And a Mad Scientist who must Battle a Teacher in The Canadian Rockies',
                2006,
                new SakilaLanguage(1, 'English', '2006-02-15 05:02:19')
            ),
            $filmRepo->find(1)
        );
    }
}
