<?php
namespace Wandu\Http\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Wandu\Http\Psr\Stream\StringStream;
use function Wandu\Http\response;

class HttpExceptionTest extends TestCase
{
    public function testCreate()
    {
        $exception = new HttpException();
        static::assertSame(200, $exception->getStatusCode());
        static::assertSame('OK', $exception->getReasonPhrase());
        static::assertNull($exception->getBody());

        $exception = new HttpException('Page Not Found!');
        static::assertSame(200, $exception->getStatusCode());
        static::assertSame('OK', $exception->getReasonPhrase());
        static::assertSame('Page Not Found!', $exception->getBody()->__toString());

        $exception = new HttpException(new StringStream('Page Not Found...'));
        static::assertSame(200, $exception->getStatusCode());
        static::assertSame('OK', $exception->getReasonPhrase());
        static::assertSame('Page Not Found...', $exception->getBody()->__toString());
    }

    public function provideExceptions()
    {
        return [
            [new BadRequestException(), 400, 'Bad Request'],
            [new ForbiddenException(), 403, 'Forbidden'],
            [new InternalServerErrorException(), 500, 'Internal Server Error'],
            [new MethodNotAllowedException(), 405, 'Method Not Allowed'],
            [new NotFoundException(), 404, 'Not Found'],
            [new UnauthorizedException(), 401, 'Unauthorized'],
        ];
    }

    /**
     * @dataProvider provideExceptions
     * @param \Wandu\Http\Exception\HttpException $exception
     * @param int $statusCode
     * @param string $reasonPhrase
     */
    public function testOtherHttpExceptions(HttpException $exception, $statusCode, $reasonPhrase)
    {
        static::assertSame($statusCode, $exception->getStatusCode());
        static::assertSame($reasonPhrase, $exception->getReasonPhrase());
        static::assertNull($exception->getBody());
    }

    public function testGetDefaultValues()
    {
        $httpException = new InternalServerErrorException();

        static::assertSame(500, $httpException->getStatusCode());
        static::assertSame("Internal Server Error", $httpException->getReasonPhrase());
        
        $response = response()->string('Hello World?');
        $httpException = new InternalServerErrorException($response);

        static::assertSame(500, $httpException->getStatusCode());
        static::assertSame("Hello World?", $httpException->getBody()->__toString());
    }

    public function testWithMethods()
    {
        $httpException = new InternalServerErrorException();

        // withBody
        $stream = new StringStream();
        $clonedException = $httpException->withBody($stream);
        
        static::assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        static::assertInstanceOf(HttpException::class, $clonedException);
        static::assertSame($stream, $clonedException->getBody());

        // withHeader
        $clonedException = $httpException->withHeader('content-type', 'application/json');

        static::assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        static::assertInstanceOf(HttpException::class, $clonedException);
        static::assertSame('application/json', $clonedException->getHeaderLine('Content-Type'));
        
        // withAddedHeader
        $clonedException = $httpException->withAddedHeader('content-type', 'application/json');
        static::assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        static::assertInstanceOf(HttpException::class, $clonedException);
        static::assertSame('application/json', $clonedException->getHeaderLine('Content-Type'));
        
        // withoutHeader
        $clonedException = $httpException
            ->withHeader('content-type', 'application/json')
            ->withoutHeader('content-type');

        static::assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        static::assertInstanceOf(HttpException::class, $clonedException);
        static::assertSame('', $clonedException->getHeaderLine('Content-Type'));
        
        
        // withProtocolVersion
        $clonedException = $httpException->withProtocolVersion('2.0');
        static::assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        static::assertInstanceOf(HttpException::class, $clonedException);
        static::assertSame('2.0', $clonedException->getProtocolVersion('Content-Type'));


        // withStatus
        $clonedException = $httpException->withStatus(404, 'what..');
        static::assertNotInstanceOf(InternalServerErrorException::class, $clonedException);
        static::assertInstanceOf(HttpException::class, $clonedException);
        static::assertSame(404, $clonedException->getStatusCode());
        static::assertSame('what..', $clonedException->getReasonPhrase());
    }

    public function testGetResposeByDefault()
    {
        $httpException = new InternalServerErrorException();

        $response = $httpException->getResponse();

        static::assertInstanceOf(ResponseInterface::class, $response);

        static::assertSame(500, $response->getStatusCode());
        static::assertSame('Internal Server Error', $response->getReasonPhrase());
        static::assertNull($response->getBody());
        static::assertSame('1.1', $response->getProtocolVersion());
        static::assertEquals([], $response->getHeaders());
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

        static::assertInstanceOf(ResponseInterface::class, $response);

        // it must be 500. because name is InternelServerErrorException
        static::assertSame(500, $response->getStatusCode());
        static::assertSame('Internal Server Error', $response->getReasonPhrase());
        
        static::assertInstanceOf(StringStream::class, $response->getBody());
        static::assertSame('1.0', $response->getProtocolVersion());
        static::assertEquals(['content-type' => ['application/json']], $response->getHeaders());
    }
}
