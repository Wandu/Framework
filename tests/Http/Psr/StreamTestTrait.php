<?php
namespace Wandu\Http\Psr;

use RuntimeException;

trait StreamTestTrait
{
    /** @var \Psr\Http\Message\StreamInterface */
    protected $stream;

    public function testWrite()
    {
        $this->stream->write("what the?");
        static::assertEquals('what the?', $this->stream->__toString());
    }

    public function testGetMetadata()
    {
        static::assertTrue(is_array($this->stream->getMetadata()));
        static::assertNull($this->stream->getMetadata('unknown.........'));
    }

    public function testSeek()
    {
        $this->stream->write("Hello World");
        try {
            $this->stream->seek(100);
            static::fail();
        } catch (RuntimeException $e) {
        }
        $this->stream->seek(6);

        static::assertEquals('World', $this->stream->getContents());
    }

    public function testReadAndWrite()
    {
        static::assertEquals(0, $this->stream->getSize());

        $this->stream->write("Hello World, And All Developers!");

        static::assertEquals(32, $this->stream->getSize()); // size
        static::assertEquals(32, $this->stream->tell()); // pointer

        $this->stream->rewind();

        static::assertEquals(0, $this->stream->tell());
        static::assertFalse($this->stream->eof());


        static::assertEquals("Hell", $this->stream->read(4));
        static::assertEquals("o World, ", $this->stream->read(9));
        static::assertEquals("And All Developers!", $this->stream->getContents());

        static::assertTrue($this->stream->eof());

        $this->stream->seek(12);
        static::assertEquals(6, $this->stream->write('Hum...'));

        static::assertEquals("ll Developers!", $this->stream->getContents());
        static::assertEquals("Hello World,Hum...ll Developers!", $this->stream->__toString());
    }

    public function testNullToString()
    {
        static::assertSame('', $this->stream->__toString());
    }
}
