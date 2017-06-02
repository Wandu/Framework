<?php
namespace Wandu\Http\File;

use Mockery;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use Wandu\Http\Psr\UploadedFile;

class UploaderTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
        static::addToAssertionCount(1);
    }

    public function testSuccessToConstruct()
    {
        new Uploader(__DIR__);

        // not exists directory
        new Uploader(__DIR__ . '/notexists', true);
        rmdir(__DIR__ . '/notexists');
    }

    public function testFailToConstruct()
    {
        // file
        try {
            new Uploader(__FILE__);
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::addToAssertionCount(1);
        }

        // not exists directory
        try {
            new Uploader(__DIR__ . '/notexists');
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::addToAssertionCount(1);
        }

        // do not permit directory
        mkdir(__DIR__ . '/cannot', 0);
        try {
            new Uploader(__DIR__ . '/cannot');
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::addToAssertionCount(1);
        }

        rmdir(__DIR__ . '/cannot');
    }

    public function testUploadFile()
    {
        $uploader = new Uploader(__DIR__);

        // has error return null (and not any action)
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getError')->once()->andReturn(UploadedFile::ERR_NO_FILE);
        $file->shouldReceive('moveTo')->never();

        static::assertNull($uploader->uploadFile($file));

        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getError')->once()->andReturn(UploadedFile::OK);
        $file->shouldReceive('getClientFilename')->once()->andReturn('helloworld.png');
        $file->shouldReceive('moveTo')->once();

        static::assertRegExp('/\d{6}\\/[0-9a-f]{40}\\.png/', $file = $uploader->uploadFile($file));
        static::asserttrue(is_dir(__DIR__ . '/' .pathinfo($file)['dirname'])); // auto dir created

        @rmdir(__DIR__ . '/' .date('ymd'));
    }

    public function testUploadFiles()
    {
        $erroredFile = Mockery::mock(UploadedFile::class);
        $erroredFile->shouldReceive('getError')->andReturn(UploadedFile::ERR_NO_FILE);

        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getError')->andReturn(UploadedFile::OK);
        $file->shouldReceive('getClientFilename')->andReturn('helloworld.png');
        $file->shouldReceive('moveTo');

        $uploader = new Uploader(__DIR__);
        $result = $uploader->uploadFiles([
            'foo' => $file,
            'bar' => $file,
            'baz' => [
                $file, $erroredFile, $file, $file,
            ],
            'qux' => $erroredFile,
        ]);

        static::assertTrue(is_string($result['foo']));
        static::assertTrue(is_string($result['bar']));
        static::assertTrue(is_string($result['baz'][0]));
        static::assertFalse(array_key_exists(1, $result['baz']));
        static::assertTrue(is_string($result['baz'][2]));
        static::assertTrue(is_string($result['baz'][3]));

        static::assertFalse(array_key_exists('qux', $result));

        @rmdir(__DIR__ . '/' .date('ymd'));
    }
}
