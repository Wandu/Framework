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
        
        static::assertEqualsAndSameProperty(
            new SakilaLanguage(1, 'English', new Carbon('2006-02-15 05:02:19')),
            $languageRepo->find(1)
        );
    }
}
