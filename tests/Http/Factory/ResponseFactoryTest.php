<?php
namespace Wandu\Http\Factory;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseFactoryTest extends TestCase
{
    /** @var \Wandu\Http\Factory\ResponseFactory */
    protected $factory;

    public function setUp()
    {
        $this->factory = new ResponseFactory();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCapture()
    {
        $response = $this->factory->capture(function () {
            echo "Hello World!\n";
        });

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals(<<<TXT
Hello World!

TXT
        , $response->getBody()->__toString());
    }

    public function testJson()
    {
        $response = $this->factory->json(['hello' => 'hello string!', ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals("application/json", $response->getHeaderLine('Content-Type'));
        static::assertEquals(json_encode(['hello' => 'hello string!', ]), $response->getBody()->__toString());
    }
    
    public function testRedirect()
    {
        $response = $this->factory->redirect('/hello-world');

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals("/hello-world", $response->getHeaderLine('Location'));
        static::assertNull($response->getBody());
    }

    public function testIterator()
    {
        $iterator = function () {
            for ($i = 0; $i < 10; $i++) {
                yield $i => $i . "\n";
            }
        };
        $response = $this->factory->iterator($iterator());

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertEquals("0\n1\n2\n3\n4\n5\n6\n7\n8\n9\n", $response->getBody()->__toString());
    }
}
