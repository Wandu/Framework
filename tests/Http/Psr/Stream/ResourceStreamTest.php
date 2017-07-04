<?php
namespace Wandu\Http\Psr\Stream;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Wandu\Http\Psr\StreamTestTrait;

class ResourceStreamTest extends TestCase
{
     use StreamTestTrait;

    public function setUp()
    {
        file_put_contents(__DIR__ . '/resource.txt', '');
        $this->stream = new ResourceStream(fopen(__DIR__ . '/resource.txt', 'r+'));
    }
    
    public function testIsAble()
    {
        $stream = new ResourceStream(fopen(__DIR__ . '/resource.txt', 'r'));
        static::assertTrue($stream->isReadable());
        static::assertTrue($stream->isSeekable());
        static::assertFalse($stream->isWritable());

        $stream = new ResourceStream(fopen(__DIR__ . '/resource.txt', 'r+'));
        static::assertTrue($stream->isReadable());
        static::assertTrue($stream->isSeekable());
        static::assertTrue($stream->isWritable());

        $stream = new ResourceStream(fopen(__DIR__ . '/resource.txt', 'w'));
        static::assertFalse($stream->isReadable());
        static::assertTrue($stream->isSeekable());
        static::assertTrue($stream->isWritable());

        $stream = new ResourceStream(fopen(__DIR__ . '/resource.txt', 'w+'));
        static::assertTrue($stream->isReadable());
        static::assertTrue($stream->isSeekable());
        static::assertTrue($stream->isWritable());

        $stream = new ResourceStream(fopen(__DIR__ . '/resource.txt', 'a'));
        static::assertFalse($stream->isReadable());
        static::assertTrue($stream->isSeekable());
        static::assertFalse($stream->isWritable());

        $stream = new ResourceStream(fopen(__DIR__ . '/resource.txt', 'a+'));
        static::assertTrue($stream->isReadable());
        static::assertTrue($stream->isSeekable());
        static::assertTrue($stream->isWritable());
    }

    public function testGetMetaDataSeekable()
    {
        static::assertEquals(1, $this->stream->getMetadata('seekable'));
    }

    public function testCloseAndException()
    {
        $stream = new ResourceStream(fopen('php://memory', 'w+'));

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
            static::fail();
        } catch (RuntimeException $e) {
            static::assertEquals('No resource available.', $e->getMessage());
        }
    }
}
