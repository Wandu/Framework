<?php
namespace Wandu\Foundation\Error;

use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Wandu\Config\Config;
use Wandu\DI\Container;
use Wandu\Http\Exception\NotFoundException;
use Wandu\Http\Psr\ServerRequest;
use Exception;
use Wandu\Http\Psr\Stream\StringStream;
use Wandu\Http\Psr\Uri;

class DefaultHttpErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var \Psr\Log\LoggerInterface $logger */
    protected $logger;
    
    public function setUp()
    {
        $this->logger = Mockery::mock(LoggerInterface::class);
    }
    
    public function tearDown()
    {
        Mockery::close();
    }

    public function testHandleException()
    {
        $this->logger->shouldReceive('error')->twice(); // exception is error
        
        $handler = new DefaultHttpErrorHandler($this->logger, new Config(['debug' => false]));
        $response = $handler->handle(
            (new ServerRequest())->withUri(new Uri('/')),
            new Exception()
        );
        
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('500 Internal Server Error', $response->getBody()->__toString());
    }

    public function testHandleHttpException()
    {
        $this->logger->shouldReceive('notice')->twice(); // http exception is notice
        
        $handler = new DefaultHttpErrorHandler($this->logger, new Config(['debug' => false]));
        $response = $handler->handle(
            (new ServerRequest())->withUri(new Uri('/')),
            (new NotFoundException())->withBody(new StringStream('404 Not Found...'))
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('404 Not Found...', $response->getBody()->__toString());
    }

    public function testHandleDebugMode()
    {
        $handler = new DefaultHttpErrorHandler($this->logger, new Config(['debug' => true]));
        $response = $handler->handle(
            (new ServerRequest())->withUri(new Uri('/')),
            new Exception()
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        
        // never print original text...
        $this->assertNotEquals('500 Internal Server Error', $response->getBody()->__toString());
        
        $this->assertTrue(is_string($response->getBody()->__toString()));
    }

    public function testHandleDebugModeButHttpException()
    {
        $this->logger->shouldReceive('notice')->twice(); // http exception is notice

        $handler = new DefaultHttpErrorHandler($this->logger, new Config(['debug' => true]));
        $response = $handler->handle(
            (new ServerRequest())->withUri(new Uri('/')),
            (new NotFoundException())->withBody(new StringStream('404 Not Found...'))
        );

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('404 Not Found...', $response->getBody()->__toString());
    }
}
