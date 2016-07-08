<?php
namespace Wandu\Http\Exception;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Wandu\Http\Psr\Stream\StringStream;
use RuntimeException;
use function Wandu\Http\response;

class HttpExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetDefaultValues()
    {
        $httpException = new InternalServerErrorException();

        $this->assertSame(500, $httpException->getStatusCode());
        $this->assertSame("Internal Server Error", $httpException->getReasonPhrase());
        
        $response = response()->create('Hello World?');
        $httpException = new InternalServerErrorException($response);

        $this->assertSame(500, $httpException->getStatusCode());
        $this->assertSame("Hello World?", $httpException->getBody()->__toString());
    }

    public function testCannotCallWithMethods()
    {
        $httpException = new InternalServerErrorException();

        try {
            $httpException->withBody(new StringStream());
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change body in Wandu\Http\Exception\InternalServerErrorException.', $e->getMessage());
        }

        try {
            $httpException->withHeader('content-type', 'application/json');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change header in Wandu\Http\Exception\InternalServerErrorException.', $e->getMessage());
        }

        try {
            $httpException->withAddedHeader('content-type', 'application/json');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change header in Wandu\Http\Exception\InternalServerErrorException.', $e->getMessage());
        }

        try {
            $httpException->withoutHeader('content-type');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change header in Wandu\Http\Exception\InternalServerErrorException.', $e->getMessage());
        }

        try {
            $httpException->withProtocolVersion('2.0');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change protocolVersion in Wandu\Http\Exception\InternalServerErrorException.', $e->getMessage());
        }

        try {
            $httpException->withStatus(404, 'what..');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change status in Wandu\Http\Exception\InternalServerErrorException.', $e->getMessage());
        }
    }

    public function testGetResposeByDefault()
    {
        $httpException = new InternalServerErrorException();

        $response = $httpException->getResponse();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Internal Server Error', $response->getReasonPhrase());
        $this->assertNull($response->getBody());
        $this->assertSame('1.1', $response->getProtocolVersion());
        $this->assertEquals([], $response->getHeaders());
    }

    public function testGetRespose()
    {
        $httpException = new InternalServerErrorException(
            new \Wandu\Http\Psr\Response(
                400,
                new StringStream(),
                ['content-type' => 'application/json'],
                'other reason-phrase',
                '1.0'             
            )
        );

        $response = $httpException->getResponse();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        // it must be 500. because name is InternelServerErrorException
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Internal Server Error', $response->getReasonPhrase());
        
        $this->assertInstanceOf(StringStream::class, $response->getBody());
        $this->assertSame('1.0', $response->getProtocolVersion());
        $this->assertEquals(['content-type' => 'application/json'], $response->getHeaders());
    }
}
