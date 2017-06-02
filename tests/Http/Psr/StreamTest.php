<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Mockery;
use RuntimeException;

class StreamTest extends TestCase
{
    use StreamTestTrait;

    public function setUp()
    {
        $this->stream = new Stream('php://memory', 'w+');
    }

    public function testConstruct()
    {
        new Stream();
        new Stream('php://input');

        try {
            new Stream('unknown');
        } catch (InvalidArgumentException $e) {
            static::assertequals(
                'Invalid stream "unknown". It must be a valid path with valid permissions.',
                $e->getMessage()
            );
        }
    }

    public function testGetMetaDataSeekable()
    {
        static::assertEquals(1, $this->stream->getMetadata('seekable'));
    }

    public function testIsWritableAndReadable()
    {
        $fileName = tempnam(__DIR__, '_none_');

        $stream = new Stream($fileName, "r");

        static::assertFalse($stream->isWritable());
        static::assertTrue($stream->isReadable());
        try {
            $stream->write('...');
            $this->fail();
        } catch (RuntimeException $e) {
            static::assertEquals('Stream is not writable.', $e->getMessage());
        }

        $stream = new Stream($fileName, "w");

        static::assertTrue($stream->isWritable());
        static::assertFalse($stream->isReadable());
        try {
            $stream->read(1);
            $this->fail();
        } catch (RuntimeException $e) {
            static::assertEquals('Stream is not readable.', $e->getMessage());
        }

        $stream = new Stream($fileName, "r+");

        static::assertTrue($stream->isWritable());
        static::assertTrue($stream->isReadable());

        $stream = new Stream($fileName, "w+");

        static::assertTrue($stream->isWritable());
        static::assertTrue($stream->isReadable());

        @unlink($fileName);
    }

    public function testCloseAndException()
    {
        $stream = new Stream('php://memory', 'w+');

        $stream->close();
        $stream->close();

        static::assertFalse($stream->isWritable());
        static::assertFalse($stream->isReadable());
        static::assertFalse($stream->isSeekable());
        static::assertSame('', $stream->__toString());
        static::assertNull($stream->getSize());
        static::assertTrue($stream->eof());
        try {
            $stream->write('...?');
            $this->fail();
        } catch (RuntimeException $e) {
            static::assertEquals('No resource available.', $e->getMessage());
        }
    }
}
