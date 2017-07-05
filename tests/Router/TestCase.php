<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Loader\DefaultLoader;
use Wandu\Router\Responsifier\PsrResponsifier;

class TestCase extends PHPUnitTestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @param string $method
     * @param string $path
     * @param string $host
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function createRequest($method, $path, $host = 'wandu.io')
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getHeaderLine')->with('host')->andReturn($host);
        $mockRequest->shouldReceive('getMethod')->andReturn($method);
        $mockRequest->shouldReceive('getUri->getPath')->andReturn($path);
        return $mockRequest;
    }

    /**
     * @param array $config
     * @return \Wandu\Router\Dispatcher
     */
    public function createDispatcher(array $config = [])
    {
        return new Dispatcher(
            new DefaultLoader(),
            new PsrResponsifier(),
            new Configuration($config)
        );
    }
}
