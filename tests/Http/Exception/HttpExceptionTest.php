<?php
namespace Wandu\Http\Exception;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Wandu\Http\Psr\Stream\StringStream;
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

    public function testWithMethods()
    {
        $httpException = new InternalServerErrorException();

        // withBody
        $stream = new StringStream();
        $clonedException = $httpException->withBody($stream);
        
        $this->assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        $this->assertInstanceOf(HttpException::class, $clonedException);
        $this->assertSame($stream, $clonedException->getBody());

        // withHeader
        $clonedException = $httpException->withHeader('content-type', 'application/json');

        $this->assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        $this->assertInstanceOf(HttpException::class, $clonedException);
        $this->assertSame('application/json', $clonedException->getHeaderLine('Content-Type'));
        
        // withAddedHeader
        $clonedException = $httpException->withAddedHeader('content-type', 'application/json');
        $this->assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        $this->assertInstanceOf(HttpException::class, $clonedException);
        $this->assertSame('application/json', $clonedException->getHeaderLine('Content-Type'));
        
        // withoutHeader
        $clonedException = $httpException
            ->withHeader('content-type', 'application/json')
            ->withoutHeader('content-type');

        $this->assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        $this->assertInstanceOf(HttpException::class, $clonedException);
        $this->assertSame('', $clonedException->getHeaderLine('Content-Type'));
        
        
        // withProtocolVersion
        $clonedException = $httpException->withProtocolVersion('2.0');
        $this->assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        $this->assertInstanceOf(HttpException::class, $clonedException);
        $this->assertSame('2.0', $clonedException->getProtocolVersion('Content-Type'));


        // withStatus
        $clonedException = $httpException->withStatus(404, 'what..');
        $this->assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        $this->assertInstanceOf(HttpException::class, $clonedException);
        $this->assertSame(404, $clonedException->getStatusCode());
        $this->assertSame('what..', $clonedException->getReasonPhrase());
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
