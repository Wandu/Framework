<?php
namespace Wandu\Http\Psr\Stream;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Wandu\Http\Psr\StreamTestTrait;

class StringStreamTest extends TestCase
{
    use StreamTestTrait;

    public function setUp()
    {
        $this->stream = new StringStream('');
    }

    public function testAllwaysTrueMethods()
    {
        static::assertTrue($this->stream->isReadable());
        static::assertTrue($this->stream->isSeekable());
        static::assertTrue($this->stream->isWritable());
    }

    public function testCannotUseMethods()
    {
        try {
            $this->stream->close();
            static::fail();
        } catch (RuntimeException $e) {
            static::addToAssertionCount(1);
        }
        try {
            $this->stream->detach();
            static::fail();
        } catch (RuntimeException $e) {
            static::addToAssertionCount(1);
        }
    }
    
    public function testAddedWrite()
    {
        $stream = new StringStream("hello world");
        $stream->write(", and all!");
        static::assertEquals("hello world, and all!", $stream->__toString());
    }
}
