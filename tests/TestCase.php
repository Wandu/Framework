<?php
namespace Wandu\Router;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @param string $method
     * @param string $path
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function createRequest($method, $path)
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getMethod')->andReturn($method);
        $mockRequest->shouldReceive('getUri->getPath')->andReturn($path);
        return $mockRequest;
    }
}
