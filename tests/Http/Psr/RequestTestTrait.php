<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use Mockery;
use Psr\Http\Message\UriInterface;

trait RequestTestTrait
{
    /** @var \Psr\Http\Message\RequestInterface */
    protected $request;

    public function testGetRequestTarget()
    {
        $request = $this->request;
        $requestWithBlank = $this->request->withRequestTarget('');

        static::assertSame('/', $request->getRequestTarget());
        static::assertSame('/', $requestWithBlank->getRequestTarget());
    }

    public function testWithRequestTarget()
    {
        static::assertNotSame($this->request, $this->request->withRequestTarget('/abc/def'));
        static::assertEquals('/abc/def', $this->request->withRequestTarget('/abc/def')->getRequestTarget());
    }

    public function testGetMethod()
    {
        static::assertEquals(null, $this->request->getMethod());
    }

    public function testWithMethod()
    {
        static::assertNotSame($this->request, $this->request->withMethod('post'));
        static::assertEquals('POST', $this->request->withMethod('post')->getMethod());

        try {
            $this->request->withMethod([]);
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('Unsupported HTTP method. It must be a string.', $e->getMessage());
        }
    }

    public function testGetUri()
    {
        static::assertSame(null, $this->request->getUri());
    }

    public function testWithUri()
    {
        $mockUri = Mockery::mock(UriInterface::class);
        $mockUri->shouldReceive('getPath')->andReturn('/abc/def');
        $mockUri->shouldReceive('getQuery')->andReturn('hello=world');

        $requestWithUri = $this->request->withUri($mockUri, true);

        static::assertNotSame($this->request, $requestWithUri);
        static::assertSame($mockUri, $requestWithUri->getUri());
        static::assertSame('/abc/def?hello=world', $requestWithUri->getRequestTarget());
    }

    public function testWithBlankUri()
    {
        $mockUri = Mockery::mock(UriInterface::class);
        $mockUri->shouldReceive('getPath')->andReturn('');
        $mockUri->shouldReceive('getQuery')->andReturn('');

        $requestWithUri = $this->request->withUri($mockUri, true);

        static::assertSame('/', $requestWithUri->getRequestTarget());
    }

    public function testWithUriTrue()
    {
        /**
         * You can opt-in to preserving the original state of the Host header by
         * setting `$preserveHost` to `true`. When `$preserveHost` is set to
         * `true`, this method interacts with the Host header in the following ways:
         *
         * - If the the Host header is missing or empty, and the new URI contains
         *   a host component, this method MUST update the Host header in the returned
         *   request.
         * - If the Host header is missing or empty, and the new URI does not contain a
         *   host component, this method MUST NOT update the Host header in the returned
         *   request.
         * - If a Host header is present and non-empty, this method MUST NOT update
         *   the Host header in the returned request.
        */

        $mockUri = Mockery::mock(UriInterface::class);
        $mockUri->shouldReceive('getHost')->andReturn('localhost');
        $mockUri->shouldReceive('getPort')->andReturn(8888);
        $mockUri->shouldReceive('getPath')->andReturn('/');
        $mockUri->shouldReceive('getQuery')->andReturn('');

        $requestWithUri = $this->request->withUri($mockUri);

        static::assertEquals('/', $requestWithUri->getRequestTarget());
        static::assertEquals('localhost:8888', $requestWithUri->getHeaderLine('host'));

    }
}
