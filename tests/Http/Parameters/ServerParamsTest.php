<?php
namespace Wandu\Http\Parameters;

use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Psr\ServerRequest;

class ServerParamsTest extends ParameterTest
{
    public function setUp()
    {
        $this->param1 = new ServerParams($this->createRequest($this->param1Attributes));
        $this->param2 = new ServerParams($this->createRequest($this->param2Attributes));
        $this->param3 = new ServerParams($this->createRequest($this->param3Attributes), new Parameter($this->param3FallbackAttributes));
    }

    public function testAccepts()
    {
        $request = new ServerRequest();

        $serverParams = new ServerParams($request);
        static::assertTrue($serverParams->accepts('laskdnlfasndlfk'));

        $serverParams = new ServerParams($request->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'));
        static::assertTrue($serverParams->accepts('laskdnlfasndlfk')); // because of */*

        $serverParams = new ServerParams($request->withHeader('Accept', '*,application/xhtml+xml,application/xml;q=0.9,image/webp'));
        static::assertTrue($serverParams->accepts('laskdnlfasndlfk')); // because of *

        $serverParams = new ServerParams($request->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp'));
        static::assertFalse($serverParams->accepts('unknown'));
        static::assertTrue($serverParams->accepts('html'));
        static::assertTrue($serverParams->accepts('text/html'));
        static::assertTrue($serverParams->accepts(['text/html', 'unknown']));

        $serverParams = new ServerParams($request->withHeader('Accept', 'text/*'));
        static::assertTrue($serverParams->accepts('html'));
        static::assertTrue($serverParams->accepts('text/html'));
        static::assertTrue($serverParams->accepts(['text/html', 'unknown']));

        $serverParams = new ServerParams($request->withHeader('Accept', 'application/xhtml+xml'));
        static::assertTrue($serverParams->accepts('application/xhtml+xml'));
        static::assertTrue($serverParams->accepts(['application/xhtml+xml', 'unknown']));
        static::assertTrue($serverParams->accepts(['application/xhtml+xml', 'unknown']));
        
        $serverParams = new ServerParams($request->withHeader('Accept', 'application/rss+xml,application/rdf+xml;q=0.8,application/atom+xml;q=0.6,application/xml;q=0.4,text/xml;q=0.4'));
        static::assertTrue($serverParams->accepts('rss'));
    }
    
    public function testLanguages()
    {
        $request = new ServerRequest();
        $serverParams = new ServerParams($request->withHeader('Accept-Language', 'ko,en;q=0.8,en-US;q=0.6'));
        
        static::assertEquals(['ko', 'en', 'en-US'], $serverParams->getLanguages());
    }

    public function testIsAjax()
    {
        $request = new ServerRequest();

        $serverParams = new ServerParams($request->withHeader('X-Requested-With', 'XMLHttpRequest'));
        static::assertTrue($serverParams->isAjax());

        $serverParams = new ServerParams($request->withHeader('X-Requested-With', 'nothing'));
        static::assertFalse($serverParams->isAjax());

        $serverParams = new ServerParams($request);
        static::assertFalse($serverParams->isAjax());
    }

    public function testGetIpAddress()
    {
        $request = new ServerRequest();

        $serverParams = new ServerParams($request);
        static::assertEquals(null, $serverParams->getIpAddress());

        $serverParams = new ServerParams(new ServerRequest(['REMOTE_ADDR' => '0.0.0.1']));
        static::assertEquals('0.0.0.1', $serverParams->getIpAddress());

        $serverParams = new ServerParams($request->withHeader('x-forwarded-for', '0.0.0.2'));
        static::assertEquals('0.0.0.2', $serverParams->getIpAddress());

        $serverParams = new ServerParams((new ServerRequest(['REMOTE_ADDR' => '0.0.0.3']))->withHeader('x-forwarded-for', '0.0.0.4'));
        static::assertEquals('0.0.0.4', $serverParams->getIpAddress()); // x-forwarded-for first
    }

    /**
     * @param array $attributes
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function createRequest(array $attributes = [])
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getServerParams')->andReturn($attributes);
        return $request;
    }
}
