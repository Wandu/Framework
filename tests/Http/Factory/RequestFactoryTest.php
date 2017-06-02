<?php
namespace Wandu\Http\Factory;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class RequestFactoryTest extends TestCase
{
    /** @var \Wandu\Http\Factory\RequestFactory */
    protected $factory;

    public function setUp()
    {
        $this->factory = new RequestFactory();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateRequest()
    {
        $request = $this->factory->createRequest([
            'GET /hello/world HTTP/1.1',
            'Host: localhost',
            'User-Agent: Safari/537.36',
            'Referer: http://localhost/',
            'Cookie: FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
        ]);

        static::assertEquals('GET', $request->getMethod());
        static::assertEquals('http://localhost/hello/world', $request->getUri()->__toString());
        static::assertEquals('1.1', $request->getProtocolVersion());

        static::assertEquals('localhost', $request->getHeaderLine('host'));
        static::assertEquals('Safari/537.36', $request->getHeaderLine('user-agent'));
        static::assertEquals('http://localhost/', $request->getHeaderLine('referer'));
        static::assertEquals(
            'FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
            $request->getHeaderLine('cookie')
        );

        $request = $this->factory->createRequest([
            'GET /hello/world HTTP/1.0',
            'Host: localhost',
            'User-Agent: Safari/537.36',
            'Referer: http://localhost/',
            'Cookie: FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
        ]);

        static::assertEquals('GET', $request->getMethod());
        static::assertEquals('http://localhost/hello/world', $request->getUri()->__toString());
        static::assertEquals('1.0', $request->getProtocolVersion());

        static::assertEquals('localhost', $request->getHeaderLine('host'));
        static::assertEquals('Safari/537.36', $request->getHeaderLine('user-agent'));
        static::assertEquals('http://localhost/', $request->getHeaderLine('referer'));
        static::assertEquals(
            'FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
            $request->getHeaderLine('cookie')
        );
    }

    public function testCheckUriWhenCreateRequest()
    {
        $request = $this->factory->createRequest([
            'GET /hello/world HTTP/1.1',
        ]);

        static::assertEquals('/hello/world', $request->getUri()->__toString());

        $request = $this->factory->createRequest([
            'GET / HTTP/1.1',
        ]);

        static::assertEquals('/', $request->getUri()->__toString());

        $request = $this->factory->createRequest([
            'GET / HTTP/1.1',
            'Host: localhost',
        ]);

        static::assertEquals('http://localhost', $request->getUri()->__toString());
    }
}
