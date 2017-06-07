<?php
namespace Wandu\Foundation;

use Mockery;
use PHPUnit\Framework\TestCase;
use Wandu\Foundation\Contracts\Bootstrapper;
use Wandu\Foundation\Contracts\Definition;

class ApplicationTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
        $this->addToAssertionCount(1);
    }
    
    public function testBoot()
    {
        $bootstrapper = Mockery::mock(Bootstrapper::class);
        $definition = Mockery::mock(Definition::class);
        
        $app = new Application($bootstrapper, $definition);

        $bootstrapper->shouldReceive('boot')->with($app, $definition)->once();

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
        $bootstrapper = Mockery::mock(Bootstrapper::class);
        $definition = Mockery::mock(Definition::class);

        $app = new Application($bootstrapper, $definition);

        $bootstrapper->shouldReceive('boot')->with($app, $definition)->once();
        $bootstrapper->shouldReceive('execute')->with($app, $definition)->once();

        // no-call boot, but called once
        $app->execute();
    }
}
