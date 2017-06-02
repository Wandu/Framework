<?php
namespace Wandu\Http\Psr;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class RequestTest extends TestCase
{
    use RequestTestTrait, MessageTestTrait;

    public function setUp()
    {
        $this->request = $this->message = new Request();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstructor()
    {
        $request = new Request('GET', new Uri('http://wani.kr'));

        static::assertSame('http://wani.kr', $request->getUri()->__toString());

        $request = new Request('GET', 'http://wani.kr');

        static::assertInstanceOf(UriInterface::class, $request->getUri());
        static::assertSame('http://wani.kr', $request->getUri()->__toString());
    }
}
