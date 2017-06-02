<?php
namespace Wandu\DI;

use Mockery;
use PHPUnit\Framework\TestCase;

class ServiceProviderTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
        static::addToAssertionCount(1);
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

        $mockery = Mockery::mock(ServiceProviderTestInjector::class);

        $mockery->shouldReceive('register')->once();
        $mockery->shouldReceive('boot')->never();

        $container->instance('mockery', $mockery);
        $container->register(new ServiceProviderTestBootProvider());
    }

    public function testRegisterAndBoot()
    {
        $container = new Container();

        $mockery = Mockery::mock(ServiceProviderTestInjector::class);
        $mockery->shouldReceive('register')->once();
        $mockery->shouldReceive('boot')->once();

        $container->instance('mockery', $mockery);
        $container->register(new ServiceProviderTestBootProvider());
        $container->boot();
    }
}

interface ServiceProviderTestInjector
{
    public function register();
    public function boot();
}

class ServiceProviderTestBootProvider implements ServiceProviderInterface
{
    public function register(ContainerInterface $app)
    {
        $app->get('mockery')->register();
    }

    public function boot(ContainerInterface $app)
    {
        $app->get('mockery')->boot();
    }
}
