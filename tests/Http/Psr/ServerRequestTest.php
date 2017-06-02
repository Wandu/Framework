<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Wandu\Http\Attribute\LazyAttribute;

class ServerRequestTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testSimpleConstruct()
    {
        $request = new ServerRequest();

        static::assertEquals([], $request->getServerParams());
        static::assertEquals([], $request->getCookieParams());
        static::assertEquals([], $request->getQueryParams());
        static::assertEquals([], $request->getUploadedFiles());
        static::assertEquals([], $request->getParsedBody());
        static::assertEquals([], $request->getAttributes());

        // message
        static::assertEquals('1.1', $request->getProtocolVersion());
        static::assertEquals([], $request->getHeaders());
        static::assertNull($request->getBody());

        // request
        static::assertEquals(null, $request->getMethod());
        static::assertEquals(null, $request->getUri());
        static::assertEquals('/', $request->getRequestTarget());
    }

    public function testConstructWithSuccess()
    {
        $mockFile = Mockery::mock(UploadedFileInterface::class);
        $mockUri = Mockery::mock(UriInterface::class);
        $mockUri->shouldReceive('getPath')->andReturn('/abc/def');
        $mockUri->shouldReceive('getQuery')->andReturn('hello=world');

        $request = new ServerRequest(
            [
                'SERVER_SOFTWARE' => 'PHP 5.6.8 Development Server',
                'SERVER_PROTOCOL' => 'HTTP/1.1',
                'SERVER_NAME' => '0.0.0.0',
                'SERVER_PORT' => '8002',
                'REQUEST_URI' => '/',
                'REQUEST_METHOD' => 'POST',
                'PHP_SELF' => '/index.php',
                'HTTP_HOST' => 'localhost:8002',
                'HTTP_CONNECTION' => 'keep-alive',
                'HTTP_CONTENT_LENGTH' => '56854',
                'HTTP_PRAGMA' => 'no-cache',
                'HTTP_CACHE_CONTROL' => 'no-cache',
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'HTTP_ORIGIN' => 'http://localhost:8002',
                'HTTP_USER_AGENT' => 'Mozilla/5.0',
                'HTTP_COOKIE' => 'PHPSESSID=32eo4tk9dcaacb2f3hqg0s6s54',
                'REQUEST_TIME_FLOAT' => 1431675149.3160019,
                'REQUEST_TIME' => 1431675149,
            ],
            [
                'page' => 1,
                'order' => false
            ],
            ['id' => 'wan2land'],
            ['PHPSESSID' => '32eo4tk9dcaacb2f3hqg0s6s54'],
            ['profileImage' => $mockFile],
            ['status' => 'join'],
            'GET',
            $mockUri,
            null,
            [
                'host' => ['localhost:8002'],
                'connection' => ['keep-alive'],
                'user-agent' => ['Mozilla/5.0'],
                'cookie' => ['PHPSESSID=32eo4tk9dcaacb2f3hqg0s6s54'],
            ],
            '2.0'
        );
        static::assertEquals(['PHPSESSID' => '32eo4tk9dcaacb2f3hqg0s6s54'], $request->getCookieParams());
        static::assertEquals(['page' => 1, 'order' => false], $request->getQueryParams());
        static::assertEquals(['profileImage' => $mockFile], $request->getUploadedFiles());
        static::assertEquals(['id' => 'wan2land'], $request->getParsedBody());
        static::assertEquals(['status' => 'join'], $request->getAttributes());

        // message
        static::assertEquals('2.0', $request->getProtocolVersion());
        static::assertEquals([
            'host' => ['localhost:8002'],
            'connection' => ['keep-alive'],
            'user-agent' => ['Mozilla/5.0'],
            'cookie' => ['PHPSESSID=32eo4tk9dcaacb2f3hqg0s6s54'],
        ], $request->getHeaders());
    }

    public function testConstructWithFail()
    {
        try {
            new ServerRequest([], [], [], [], ['hello' => ['world' => new \stdClass()]]);
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals(
                'Invalid uploaded files value. It must be a array of UploadedFileInterface.',
                $e->getMessage()
            );
        }
    }

    public function testWithCookieParams()
    {
        $request = new ServerRequest();

        static::assertNotSame($request, $request->withCookieParams([]));
        static::assertEquals(
            ['other' => 'blabla'],
            $request->withCookieParams(['other' => 'blabla'])->getCookieParams()
        );
    }

    public function testWithQueryParams()
    {
        $request = new ServerRequest();
        static::assertNotSame($request, $request->withQueryParams([]));
        static::assertEquals(
            ['other' => 'blabla'],
            $request->withQueryParams(['other' => 'blabla'])->getQueryParams()
        );
    }

    public function testWithUploadedFiles()
    {
        $mockFile = Mockery::mock(UploadedFileInterface::class);

        $request = new ServerRequest();

        static::assertNotSame($request, $request->withUploadedFiles([]));
        static::assertEquals(
            ['main' => $mockFile],
            $request->withUploadedFiles(['main' => $mockFile])->getUploadedFiles()
        );
    }

    public function testWithParsedBody()
    {
        $request = new ServerRequest();

        static::assertNotSame($request, $request->withParsedBody([]));
        static::assertEquals(
            ['main' => 'hello?'],
            $request->withParsedBody(['main' => 'hello?'])->getParsedBody()
        );
    }

    public function testGetAttribute()
    {
        $request = new ServerRequest([], [], [], [], [], [
            'id' => 'wan2land',
            'status' => 'modify'
        ]);
        static::assertEquals('wan2land', $request->getAttribute('id'));
        static::assertEquals('modify', $request->getAttribute('status'));

        static::assertNull($request->getAttribute('unknown'));
        static::assertEquals('default', $request->getAttribute('unknown', 'default'));
    }

    public function testWithAttribute()
    {
        $request = new ServerRequest();

        static::assertNotSame($request, $request->withAttribute('name', 30));
        static::assertEquals([
            'name' => 30
        ], $request->withAttribute('name', 30)->getAttributes());
    }

    public function testWithoutAttribute()
    {
        $request = new ServerRequest([], [], [], [], [], [
            'id' => 'wan2land',
            'status' => 'modify'
        ]);

        static::assertNotSame($request, $request->withoutAttribute('id'));
        static::assertNotSame($request, $request->withoutAttribute('unknown'));

        static::assertEquals([
            'status' => 'modify'
        ], $request->withoutAttribute('id')->getAttributes());
    }
    
    public function testLazyWithAttribute()
    {
        $request = new ServerRequest();
        
        $request = $request->withAttribute('hello', new LazyAttribute(function () {
            return "hello world!!!";
        }));
        
        $request = unserialize(serialize($request));

        static::assertEquals('hello world!!!', $request->getAttribute('hello'));
    }

    public function testLazyWithAttributeAsSingleton()
    {
        $request = new ServerRequest();

        $request = $request->withAttribute('array', new LazyAttribute(function () {
            return new \ArrayObject();
        }));

        static::assertSame($request->getAttribute('array'), $request->getAttribute('array'));
    }
}
