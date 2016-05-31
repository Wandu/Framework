<?php
namespace Wandu\DI;

use Mockery;
use Wandu\DI\Stub\ServiceProvider\BootProvider;
use Wandu\DI\Stub\ServiceProvider\ProviderCheckable;

class ServiceProviderTest extends TestCase
{
    public function testRegister()
    {
        $mockProvider = Mockery::mock(ServiceProviderInterface::class);
        $mockProvider->shouldReceive('register')->once()->with($this->container);

        $this->container->register($mockProvider);
    }

    public function testOnlyRegister()
    {
        $mockery = Mockery::mock(ProviderCheckable::class);

        $mockery->shouldReceive('register')->once();
        $mockery->shouldReceive('boot')->never();

        $this->container->instance('mockery', $mockery);
        $this->container->register(new BootProvider());
    }

    public function testRegisterAndBoot()
    {
        $mockery = Mockery::mock(ProviderCheckable::class);
        $mockery->shouldReceive('register')->once();
        $mockery->shouldReceive('boot')->once();

        $this->container->instance('mockery', $mockery);
        $this->container->register(new BootProvider());
        $this->container->boot();
    }
}
