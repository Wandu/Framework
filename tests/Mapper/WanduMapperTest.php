<?php
namespace Wandu\Router\Mapper;

use Wandu\DI\ContainerInterface;
use Wandu\Router\Middleware\MiddlewareInterface;
use Wandu\Router\Stubs\AdminController;
use PHPUnit_Framework_TestCase;
use Mockery;
use Wandu\Router\Stubs\AuthMiddleware;

class WanduMapperTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testMapHandler()
    {
        $mockContainer = Mockery::mock(ContainerInterface::class);
        $mockContainer->shouldReceive('create')
            ->with('Wandu\\Router\\Stubs\\AdminController')
            ->andReturn($controller = new AdminController);

        $mapper = new WanduMapper($mockContainer, 'Wandu\\Router\\Stubs', 'Wandu\\Router\\Stubs');

        $result = $mapper->mapHandler('index@AdminController');

        $this->assertTrue(is_callable($result));
        $this->assertEquals([$controller, 'index'], $result);
    }

    public function testMapMiddleware()
    {
        $mockContainer = Mockery::mock(ContainerInterface::class);
        $mockContainer->shouldReceive('create')
            ->with('Wandu\\Router\\Stubs\\AuthMiddleware')
            ->andReturn($controller = new AuthMiddleware);

        $mapper = new WanduMapper($mockContainer, 'Wandu\\Router\\Stubs', 'Wandu\\Router\\Stubs');

        $result = $mapper->mapMiddleware('AuthMiddleware');

        $this->assertSame($controller, $result);
        $this->assertInstanceOf(MiddlewareInterface::class, $result);
    }
}
