<?php
namespace Wandu\Foundation\Error;

use PHPUnit\Framework\TestCase;
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

class DefaultHttpErrorHandlerTest extends TestCase
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
        
        $handler = new DefaultHttpErrorHandler(new Config(['debug' => false]), $this->logger);
        $response = $handler->handle(
            (new ServerRequest())->withUri(new Uri('/')),
            new Exception()
        );
        
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals(500, $response->getStatusCode());
        static::assertEquals('500 Internal Server Error', $response->getBody()->__toString());
    }

    public function testHandleHttpException()
    {
        $this->logger->shouldReceive('notice')->twice(); // http exception is notice
        
        $handler = new DefaultHttpErrorHandler(new Config(['debug' => false]), $this->logger);
        $response = $handler->handle(
            (new ServerRequest())->withUri(new Uri('/')),
            (new NotFoundException())->withBody(new StringStream('404 Not Found...'))
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals(404, $response->getStatusCode());
        static::assertEquals('404 Not Found...', $response->getBody()->__toString());
    }

    public function testHandleDebugMode()
    {
        $handler = new DefaultHttpErrorHandler(new Config(['debug' => true]), $this->logger);
        $response = $handler->handle(
            (new ServerRequest())->withUri(new Uri('/')),
            new Exception()
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals(500, $response->getStatusCode());
        
        // never print original text...
        static::assertNotEquals('500 Internal Server Error', $response->getBody()->__toString());
        
        static::assertTrue(is_string($response->getBody()->__toString()));
    }

    public function testHandleDebugModeButHttpException()
    {
        $this->logger->shouldReceive('notice')->twice(); // http exception is notice

        $handler = new DefaultHttpErrorHandler(new Config(['debug' => true]), $this->logger);
        $response = $handler->handle(
            (new ServerRequest())->withUri(new Uri('/')),
            (new NotFoundException())->withBody(new StringStream('404 Not Found...'))
        );

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals(404, $response->getStatusCode());
        static::assertEquals('404 Not Found...', $response->getBody()->__toString());
    }
}
