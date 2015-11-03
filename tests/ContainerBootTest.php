<?php
namespace Wandu\DI;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\DI\Stub\Boot\BootProvider;
use Wandu\DI\Stub\Boot\ProviderCheckable;

class ContainerBootTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\DI\Container */
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $this->container = new Container();
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
