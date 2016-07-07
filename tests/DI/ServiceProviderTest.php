<?php
namespace Wandu\DI;

use Mockery;
use Wandu\DI\Stub\ServiceProvider\BootProvider;
use Wandu\DI\Stub\ServiceProvider\ProviderCheckable;
use PHPUnit_Framework_TestCase;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }
    
    public function testRegister()
    {
        $container = new Container();

        $mockProvider = Mockery::mock(ServiceProviderInterface::class);
        $mockProvider->shouldReceive('register')->once()->with($container);

        $container->register($mockProvider);
    }

    public function testOnlyRegister()
    {
        $container = new Container();

        $mockery = Mockery::mock(ProviderCheckable::class);

        $mockery->shouldReceive('register')->once();
        $mockery->shouldReceive('boot')->never();

        $container->instance('mockery', $mockery);
        $container->register(new BootProvider());
    }

    public function testRegisterAndBoot()
    {
        $container = new Container();

        $mockery = Mockery::mock(ProviderCheckable::class);
        $mockery->shouldReceive('register')->once();
        $mockery->shouldReceive('boot')->once();

        $container->instance('mockery', $mockery);
        $container->register(new BootProvider());
        $container->boot();
    }
}
