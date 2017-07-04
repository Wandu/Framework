<?php
namespace Wandu\Http\Psr\Stream;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class IteratorStreamTest extends TestCase
{
    /** @var \Wandu\Http\Psr\Stream\IteratorStream */
    protected $stream;

    public function setUp()
    {
        $generator = function () {
            for ($i = 0; $i < 10; $i++) {
                yield sprintf("%02d ", $i);
            }
        };
        $this->stream = new IteratorStream($generator());
    }

    public function testWrite()
    {
        static::assertFalse($this->stream->isWritable());
        try {
            $this->stream->write("some...");
            static::fail();
        } catch (RuntimeException $e) {
            static::assertEquals('IteratorStream cannot write.', $e->getMessage());
        }
    }

    public function testSeek()
    {
        static::assertFalse($this->stream->isSeekable());
        try {
            $this->stream->seek(0);
            static::fail();
        } catch (RuntimeException $e) {
            static::assertEquals('IteratorStream cannot seek.', $e->getMessage());
        }
    }

    public function testRead()
    {
        static::assertFalse($this->stream->isReadable());
        try {
            $this->stream->read(10);
            static::fail();
        } catch (RuntimeException $e) {
            static::assertEquals('IteratorStream cannot read.', $e->getMessage());
        }
    }

    public function testRewindAndGetContents()
    {
        static::assertFalse($this->stream->eof());
        static::assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->getContents()
        );
        static::assertTrue($this->stream->eof());
        static::assertEquals(
            '',
            $this->stream->getContents()
        );

        // rewind
        $this->stream->rewind();

        static::assertFalse($this->stream->eof());
        static::assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->getContents()
        );
        static::assertTrue($this->stream->eof());
        static::assertEquals(
            '',
            $this->stream->getContents()
        );
    }

    public function testToString()
    {
        static::assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->__toString()
        );
        static::assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->__toString()
        );
        static::assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->__toString()
        );
    }
}
