<?php
namespace Wandu\Router\Responsifier;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class PsrResponsifierTest extends TestCase 
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testReturnNull()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify(null);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame('', $response->getBody()->__toString());
    }

    public function testReturnString()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify("Hello World");

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame('Hello World', $response->getBody()->__toString());
    }

    public function testReturnInteger()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify((int) 3182);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame('3182', $response->getBody()->__toString());
    }

    public function testReturnBooleanFalse()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify(false);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame('false', $response->getBody()->__toString());
    }

    public function testReturnBooleanTrue()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify(true);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame('true', $response->getBody()->__toString());
    }

    public function testReturnFloat()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify(1.001);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame('1.001', $response->getBody()->__toString());
    }

    public function testReturnArray()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify([
            'foo' => 'foo string',
            'bar' => 3030,
        ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame([
            'foo' => 'foo string',
            'bar' => 3030,
        ], json_decode($response->getBody()->__toString(), true));
    }

    public function testReturnObject()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify((object) [
            'foo' => 'foo string',
            'bar' => 3030,
        ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame([
            'foo' => 'foo string',
            'bar' => 3030,
        ], json_decode($response->getBody()->__toString(), true));
    }

    public function testReturnReadableResource()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify(fopen(__DIR__ . '/stub-text.txt', 'r'));

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame("stub-text.txt contents\n", $response->getBody()->__toString());
    }

    public function testReturnUnreadableResource()
    {
        $responsify = new PsrResponsifier();

        try {
            $responsify->responsify(fopen(__DIR__ . '/stub-text.txt', 'a'));
            static::fail();
        } catch (RuntimeException $e) {
            static::assertEquals('Unsupported Type of Response.', $e->getMessage());
        }
    }

    public function testReturnCallable()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify(function () {
            return "Hello Word!";
        });

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame("Hello Word!", $response->getBody()->__toString());
    }

    public function testReturnMultipleCallable()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify(function () {
            return function () {
                return "Hello Word2!";
            };
        });

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame("Hello Word2!", $response->getBody()->__toString());
    }

    public function testReturnYield()
    {
        $responsify = new PsrResponsifier();

        $response = $responsify->responsify(function () {
            for ($i = 0; $i < 10; $i++) {
                yield $i;
            }
        });

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame("0123456789", $response->getBody()->__toString());
    }

    public function testYield()
    {
        $responsify = new PsrResponsifier();

        $generator = function () {
            for ($i = 0; $i < 10; $i++) {
                yield $i;
            }
        };
        
        $response = $responsify->responsify($generator());

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame("0123456789", $response->getBody()->__toString());
    }
}
