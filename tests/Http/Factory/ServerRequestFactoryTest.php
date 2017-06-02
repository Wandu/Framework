<?php
namespace Wandu\Http\Factory;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class ServerRequestFactoryTest extends TestCase
{
    /** @var \Wandu\Http\Factory\ServerRequestFactory */
    protected $factory;

    public function setUp()
    {
        $mockFileFactory = Mockery::mock(UploadedFileFactory::class);
        $mockFileFactory->shouldReceive('createFromFiles')->andReturn([]);

        $this->factory = new ServerRequestFactory($mockFileFactory);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetUriFromHeader()
    {
        $serverRequest = $this->factory->create([
            'HTTP_HOST' => 'localhost:8002',
            'REQUEST_URI' => '/abk?sdnkf',
        ], [], [], [], []);

        static::assertEquals('http://localhost:8002/abk?sdnkf', $serverRequest->getUri()->__toString());
    }

    public function testGetUriFromServerVariable()
    {
        $serverRequest = $this->factory->create([
            'SERVER_NAME' => '0.0.0.0',
            'SERVER_PORT' => '8002',
            'REQUEST_URI' => '/abk?sdnkf',
        ], [], [], [], []);

        static::assertEquals('http://0.0.0.0:8002/abk?sdnkf', $serverRequest->getUri()->__toString());
    }

    public function testGetUriFromBoth()
    {
        $serverRequest = $this->factory->create([
            'HTTP_HOST' => 'localhost:8002', // more
            'SERVER_NAME' => '0.0.0.0',
            'SERVER_PORT' => '8002',
            'REQUEST_URI' => '/abk?sdnkf',
        ], [], [], [], []);

        static::assertEquals('http://localhost:8002/abk?sdnkf', $serverRequest->getUri()->__toString());
    }

    public function testGetHeader()
    {
        $request = $this->factory->create([
            'HTTP_HOST' => 'blog.wani.kr',
            'HTTP_AUTHORIZATION_' => "something",
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_CACHE_CONTROL' => 'max-age=0',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
            'HTTP_ACCEPT_LANGUAGE' => 'ko,en;q=0.8,en-US;q=0.6',
            'HTTP_COOKIE' => 'intercom-id=b5998208-7329-4a94-a338-bdd555d042e3; wcs_bt=unknown:1470904067|s_5943a5ef73da:1472693733; _ga=GA1.2.1905714981.1465871995',
        ], [], [], [], []);

        static::assertEquals([], $request->getHeader('unknown'));

        static::assertEquals(['blog.wani.kr'], $request->getHeader('host'));
        static::assertEquals(['blog.wani.kr'], $request->getHeader('HOST'));
        static::assertEquals(['blog.wani.kr'], $request->getHeader('Host'));

        static::assertEquals('something', $request->getHeaderLine('authorization'));

        static::assertEquals(['keep-alive'], $request->getHeader('connection'));
        static::assertEquals(['max-age=0'], $request->getHeader('cache-control'));
        static::assertEquals([
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36'
        ], $request->getHeader('uSer-agEnt')); // not sensitive
        static::assertEquals([
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36'
        ], $request->getHeader('user-agent'));
        static::assertEquals([
            'text/html',
            'application/xhtml+xml',
            'application/xml;q=0.9',
            'image/webp',
            '*/*;q=0.8',
        ], $request->getHeader('accept'));
        static::assertEquals([
            'gzip',
            'deflate',
            'sdch',
        ], $request->getHeader('accept-encoding'));
        static::assertEquals([
            'ko',
            'en;q=0.8',
            'en-US;q=0.6',
        ], $request->getHeader('accept-language'));
        static::assertEquals([
            'intercom-id=b5998208-7329-4a94-a338-bdd555d042e3; wcs_bt=unknown:1470904067|s_5943a5ef73da:1472693733; _ga=GA1.2.1905714981.1465871995',
        ], $request->getHeader('cookie'));
    }

    public function testGetJsonParsedBody()
    {
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn('{"hello":[1,2,3,4,5]}');

        $serverRequest = $this->factory->create([
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], [], [], [], [], $body);

        static::assertEquals([
            'hello' => [1,2,3,4,5]
        ], $serverRequest->getParsedBody());
    }
    
    public function testServerParamsDuplicate()
    {
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn('{"hello":[1,2,3,4,5]}');

        $serverRequest = $this->factory->create([
            'HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded; charset=utf-8',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded; charset=utf-8',
            'REQUEST_METHOD' => 'PUT',
        ], [], [], [], [], $body);

        static::assertEquals([
            'content-type' => ['application/x-www-form-urlencoded; charset=utf-8'],
        ], $serverRequest->getHeaders());
    }

    public function testGetJsonParsedBodyWithCharsetHeader()
    {
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn('{"hello":[1,2,3,4,5]}');

        $serverRequest = $this->factory->create([
            'HTTP_CONTENT_TYPE' => 'application/json;charset=UTF-8',
        ], [], [], [], [], $body);

        static::assertEquals([
            'hello' => [1,2,3,4,5]
        ], $serverRequest->getParsedBody());
    }

    public function testGetFromSocketBody()
    {
        $body = <<<HTTP
GET /favicon.ico HTTP/1.1
Host: localhost
Connection: keep-alive
Pragma: no-cache
Cache-Control: no-cache
User-Agent: Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36
Referer: http://localhost/
Cookie: FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3


HTTP;
        $body = str_replace("\n", "\r\n", $body);

        $request = $this->factory->createFromSocketBody($body);

        static::assertEquals('1.1', $request->getProtocolVersion());
        static::assertEquals('GET', $request->getMethod());
        static::assertEquals('http://localhost/favicon.ico', $request->getUri()->__toString());

        static::assertEquals('localhost', $request->getHeaderLine('host'));
        static::assertEquals('keep-alive', $request->getHeaderLine('connection'));
        static::assertEquals('no-cache', $request->getHeaderLine('cache-control'));
        static::assertEquals(
            'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36',
            $request->getHeaderLine('user-agent')
        );
        static::assertEquals('http://localhost/', $request->getHeaderLine('referer'));

        static::assertEquals('http://localhost/', $request->getHeaderLine('referer'));

        static::assertEquals(
            'FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
            $request->getHeaderLine('cookie')
        );
    }
}
