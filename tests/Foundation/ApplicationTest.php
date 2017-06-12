<?php
namespace Wandu\Foundation;

use Mockery;
use PHPUnit\Framework\TestCase;
use Wandu\Foundation\Contracts\Bootstrap;

class ApplicationTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
        $this->addToAssertionCount(1);
    }
    
    public function testBoot()
    {
        $bootstrapper = Mockery::mock(Bootstrap::class);
        
        $app = new Application($bootstrapper);

        $bootstrapper->shouldReceive('boot')->with($app)->once();
        $bootstrapper->shouldReceive('providers')->andReturn([])->once();
        
        $app->boot();
        
        // call boot many times, but called once
        $app->boot();
        $app->boot();
        $app->boot();
        $app->boot();
        $app->boot();
        $app->boot();
    }

    public function testExecute()
    {
        $bootstrapper = Mockery::mock(Bootstrap::class);

        $app = new Application($bootstrapper);

        $bootstrapper->shouldReceive('providers')->andReturn([])->once();
        $bootstrapper->shouldReceive('boot')->with($app)->once();
        $bootstrapper->shouldReceive('execute')->with($app)->once();

        // no-call boot, but called once
        $app->execute();
    }
}
