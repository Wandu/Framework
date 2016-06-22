<?php
namespace Wandu\Router\ClassLoader;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\ContainerInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class WanduLoaderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreate()
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('create')->with(StubInLoader::class)
            ->once()->andReturn(new StubInLoader());

        $loader = new WanduLoader($container);

        $this->assertInstanceOf(
            StubInLoader::class,
            $loader->create(StubInLoader::class)
        );
    }

    public function testCreateFail()
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('create')->never();

        $loader = new WanduLoader($container);

        try {
            $loader->create('ThereIsNoClass');
            $this->fail();
        } catch (HandlerNotFoundException $exception) {
            $this->assertEquals('ThereIsNoClass', $exception->getClassName());
            $this->assertNull($exception->getMethodName());
        }
    }

    public function testCall()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $instance = new StubInLoader();

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('call')->with([$instance, 'callFromLoader'], [
            ServerRequestInterface::class => $request,
        ])->once()->andReturn($instance->callFromLoader());

        $loader = new WanduLoader($container);

        $this->assertEquals(
            'callFromLoader@StubInLoader',
            $loader->call($request, $instance, 'callFromLoader')
        );
    }

    public function testCallFromMagicMethod()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $instance = new StubInLoader();

        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('call')->with([$instance, '__call'], [
            'callFromMagicMethod', [ServerRequestInterface::class => $request],
        ])->once()->andReturn($instance->__call('callFromMagicMethod'));

        $loader = new WanduLoader($container);

        $this->assertEquals(
            '__call->callFromMagicMethod@StubInLoader',
            $loader->call($request, $instance, 'callFromMagicMethod')
        );
    }
}

class StubInLoader
{
    public function __call($name, $arguments = [])
    {
        return "__call->{$name}@StubInLoader";
    }

    public function callFromLoader()
    {
        return "callFromLoader@StubInLoader";
    }
}
