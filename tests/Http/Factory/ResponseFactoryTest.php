<?php
namespace Wandu\Http\Factory;

use JsonSerializable;
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

    public function testString()
    {
        $response = $this->factory->string("Hello World!\n");

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(200, $response->getStatusCode());
        static::assertSame([], $response->getHeaders());
        static::assertEquals("Hello World!\n", $response->getBody()->__toString());
    }

    public function testCustomString()
    {
        $response = $this->factory->string("Hello World!\n", 400, ['x-custom-header' => 'wow', ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("Hello World!\n", $response->getBody()->__toString());
    }

    public function testCapture()
    {
        $response = $this->factory->capture(function () {
            echo "Hello World!\n";
        });

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(200, $response->getStatusCode());
        static::assertSame([], $response->getHeaders());
        static::assertEquals("Hello World!\n", $response->getBody()->__toString());
    }

    public function testCustomCapture()
    {
        $response = $this->factory->capture(function () {
            echo "Hello World!\n";
        }, 400, ['x-custom-header' => 'wow', ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("Hello World!\n", $response->getBody()->__toString());
    }

    public function testJson()
    {
        $response = $this->factory->json(['hello' => 'hello string!', ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(200, $response->getStatusCode());
        static::assertSame([
            'Content-Type' => ['application/json', ],
        ], $response->getHeaders());
        static::assertEquals(json_encode(['hello' => 'hello string!', ]), $response->getBody()->__toString());
    }

    public function testCustomJson()
    {
        $response = $this->factory->json(['hello' => 'hello string!', ], 400, ['x-custom-header' => 'wow', ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
            'Content-Type' => ['application/json', ],
        ], $response->getHeaders());
        static::assertEquals(json_encode(['hello' => 'hello string!', ]), $response->getBody()->__toString());
    }

    public function testRedirect()
    {
        $response = $this->factory->redirect('/hello-world');

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(302, $response->getStatusCode());
        static::assertSame([
            'Location' => ['/hello-world', ],
        ], $response->getHeaders());
        static::assertNull($response->getBody());
    }

    public function testCustomRedirect()
    {
        $response = $this->factory->redirect('/hello-world', 300, ['x-custom-header' => 'wow', ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(300, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
            'Location' => ['/hello-world', ],
        ], $response->getHeaders());
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
        static::assertSame(200, $response->getStatusCode());
        static::assertSame([], $response->getHeaders());
        static::assertEquals("0\n1\n2\n3\n4\n5\n6\n7\n8\n9\n", $response->getBody()->__toString());
    }

    public function testCustomIterator()
    {
        $iterator = function () {
            for ($i = 0; $i < 10; $i++) {
                yield $i => $i . "\n";
            }
        };
        $response = $this->factory->iterator($iterator(), 400, ['x-custom-header' => 'wow', ]);

        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("0\n1\n2\n3\n4\n5\n6\n7\n8\n9\n", $response->getBody()->__toString());
    }
    
    public function testAuto()
    {
        // null
        $response = $this->factory->auto(null, 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertNull($response->getBody());

        // string
        $response = $this->factory->auto("string", 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("string", $response->getBody()->__toString());

        // scalar
        $response = $this->factory->auto(true, 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("true", $response->getBody()->__toString());

        $response = $this->factory->auto(false, 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("false", $response->getBody()->__toString());

        $response = $this->factory->auto(3030, 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("3030", $response->getBody()->__toString());

        // printable
        $response = $this->factory->auto(new ResponseFactoryTestToString(), 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("to stringed!", $response->getBody()->__toString());
        
        // json
        $response = $this->factory->auto(['body' => 'body!'], 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
            'Content-Type' => ['application/json', ],
        ], $response->getHeaders());
        static::assertEquals("{\"body\":\"body!\"}", $response->getBody()->__toString());

        $response = $this->factory->auto(new ResponseFactoryTestJsonSerializable(), 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
            'Content-Type' => ['application/json', ],
        ], $response->getHeaders());
        static::assertEquals("{\"test\":\"json serialized!\"}", $response->getBody()->__toString());

        $resource = fopen('php://memory', 'r+');
        fwrite($resource, "hello world!!");
        $response = $this->factory->auto($resource, 400, ['x-custom-header' => 'wow', ]);
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame(400, $response->getStatusCode());
        static::assertSame([
            'x-custom-header' => ['wow', ],
        ], $response->getHeaders());
        static::assertEquals("hello world!!", $response->getBody()->__toString());
    }
}

class ResponseFactoryTestJsonSerializable implements JsonSerializable
{
    function jsonSerialize()
    {
        return [
            'test' => 'json serialized!',
        ];
    }
}

class ResponseFactoryTestToString
{
    function __toString()
    {
        return 'to stringed!';
    }
}
