<?php
namespace Wandu\Http\Psr;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\UriInterface;

class RequestTest extends PHPUnit_Framework_TestCase
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

        $this->assertSame('http://wani.kr', $request->getUri()->__toString());

        $request = new Request('GET', 'http://wani.kr');

        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertSame('http://wani.kr', $request->getUri()->__toString());
    }
}
