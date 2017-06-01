<?php
namespace Wandu\Database;

use Carbon\Carbon;
use Mockery;
use Wandu\Database\Events\ExecuteQuery;
use Wandu\Database\Sakila\SakilaLanguage;
use Wandu\Event\Listener;

class OrmTest extends SakilaTestCase
{
    public function tearDown()
    {
        Mockery::close();
    }
    
    public function testLanguage()
    {
        $listener = Mockery::mock(Listener::class);
        $listener->shouldReceive('call')->with(Mockery::type(ExecuteQuery::class))->once();
        
        $this->dispatcher->on(ExecuteQuery::class, $listener);
        
        $languageRepo = $this->manager->repository(SakilaLanguage::class);
        
        /** @var \Wandu\Database\Sakila\SakilaLanguage $language */
        $language = $languageRepo->find(1);

        static::assertSame(1, $language->getId());
        static::assertSame('English', $language->getName());
        static::assertEquals(new Carbon('2006-02-15 05:02:19'), $language->getLastUpdate());
    }

    public function testInsertLanguage()
    {
        $listener = Mockery::mock(Listener::class);
        $listener->shouldReceive('call')->with(Mockery::type(ExecuteQuery::class))->times(3); // insert, update, delete
        $this->dispatcher->on(ExecuteQuery::class, $listener);

        $languageRepo = $this->manager->repository(SakilaLanguage::class);

        $language = new SakilaLanguage("Unknown", Carbon::now("Asia/Seoul"));
        static::assertNull($language->getId());

        // insert
        $languageRepo->persist($language);
        static::assertNotNull($beforeId = $language->getId());

        // update
        $languageRepo->persist($language); // update
        static::assertSame($beforeId, $language->getId()); // never change..

        // delete
        $languageRepo->delete($language); // :-)
        static::assertNull($language->getId()); // removed id..
    }
}
