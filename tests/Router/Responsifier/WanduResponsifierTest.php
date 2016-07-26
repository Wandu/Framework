<?php
namespace Wandu\Router\Responsifier;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class WanduResponsifierTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testReturnNull()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify(null);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('', $response->getBody()->__toString());
    }

    public function testReturnString()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify("Hello World");

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('Hello World', $response->getBody()->__toString());
    }

    public function testReturnInteger()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify((int) 3182);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('3182', $response->getBody()->__toString());
    }

    public function testReturnBooleanFalse()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify(false);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('false', $response->getBody()->__toString());
    }

    public function testReturnBooleanTrue()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify(true);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('true', $response->getBody()->__toString());
    }

    public function testReturnFloat()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify(1.001);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('1.001', $response->getBody()->__toString());
    }

    public function testReturnArray()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify([
            'foo' => 'foo string',
            'bar' => 3030,
        ]);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame([
            'foo' => 'foo string',
            'bar' => 3030,
        ], json_decode($response->getBody()->__toString(), true));
    }

    public function testReturnObject()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify((object) [
            'foo' => 'foo string',
            'bar' => 3030,
        ]);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame([
            'foo' => 'foo string',
            'bar' => 3030,
        ], json_decode($response->getBody()->__toString(), true));
    }

    public function testReturnReadableResource()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify(fopen(__DIR__ . '/stub-text.txt', 'r'));

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("stub-text.txt contents\n", $response->getBody()->__toString());
    }

    public function testReturnUnreadableResource()
    {
        $responsify = new WanduResponsifier();

        try {
            $responsify->responsify(fopen(__DIR__ . '/stub-text.txt', 'a'));
            $this->fail();
        } catch (Runtimeexception $e) {
            $this->assertEquals('Unsupported Type of Response.', $e->getMessage());
        }
    }

    public function testReturnCallable()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify(function () {
            return "Hello Word!";
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("Hello Word!", $response->getBody()->__toString());
    }

    public function testReturnMultipleCallable()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify(function () {
            return function () {
                return "Hello Word2!";
            };
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("Hello Word2!", $response->getBody()->__toString());
    }

    public function testReturnYield()
    {
        $responsify = new WanduResponsifier();

        $response = $responsify->responsify(function () {
            for ($i = 0; $i < 10; $i++) {
                yield $i;
            }
        });

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("0123456789", $response->getBody()->__toString());
    }

    public function testYield()
    {
        $responsify = new WanduResponsifier();

        $generator = function () {
            for ($i = 0; $i < 10; $i++) {
                yield $i;
            }
        };
        
        $response = $responsify->responsify($generator());

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame("0123456789", $response->getBody()->__toString());
    }
}
