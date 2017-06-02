<?php
namespace Wandu\Http\Session;

use PHPUnit\Framework\TestCase;

abstract class HandlerTestCase extends TestCase
{
    /** @var \SessionHandlerInterface */
    protected $adapter;

    protected function deleteAll($directory)
    {
        $files = array_diff(scandir($directory), ['.','..']);
        foreach ($files as $file) {
            is_dir("{$directory}/{$file}") ? $this->deleteAll("{$directory}/{$file}") : unlink("{$directory}/{$file}");
        }
        return rmdir($directory);
    }

    public function testEmptySession()
    {
        $session = $this->adapter->read(sha1(uniqid()));

        static::assertEquals('', $session);
    }
    
    public function testNothingToDestroy()
    {
        $sessionId = sha1(uniqid());
        $this->adapter->destroy($sessionId);
        static::addToAssertionCount(1); // no exception!
    }

    public function testMultiIdSession()
    {
        $sessionId1 = sha1(uniqid());
        $sessionId2 = sha1(uniqid());

        // write
        $this->adapter->write($sessionId1, serialize([
            'name' => 'multi session test',
        ]));

        static::assertEquals([
            'name' => 'multi session test',
        ], unserialize($this->adapter->read($sessionId1)));
        static::assertEquals('', $this->adapter->read($sessionId2));

        // destroy
        $this->adapter->destroy($sessionId2);

        static::assertEquals([
            'name' => 'multi session test',
        ], unserialize($this->adapter->read($sessionId1)));
        static::assertEquals('', $this->adapter->read($sessionId2));

        $this->adapter->destroy($sessionId1);

        // then blank
        static::assertEquals('', $this->adapter->read($sessionId1));
    }

    public function testWriteSession()
    {
        $sessionId = sha1(uniqid());

        // write
        $this->adapter->write($sessionId, serialize([
            'hello' => 'world',
            'what' => 'um..'
        ]));

        // then data
        static::assertEquals([
            'hello' => 'world',
            'what' => 'um..'
        ], unserialize($this->adapter->read($sessionId)));

        // destroy
        $this->adapter->destroy($sessionId);

        // then blank
        static::assertEquals('', $this->adapter->read($sessionId));
    }

    public function testGarbageCollection()
    {
        $countOfSessionFiles = $this->getCountOfSessionFiles();

        $this->adapter->write('1', 'string 1');
        $this->adapter->write('2', 'string 2');
        $this->adapter->write('3', 'string 3');

        // increase 3 files
        static::assertEquals($this->getCountOfSessionFiles(), $countOfSessionFiles + 3);

        static::assertTrue($this->adapter->gc(1));

        static::assertEquals('string 1', $this->adapter->read('1'));
        static::assertEquals('string 2', $this->adapter->read('2'));
        static::assertEquals('string 3', $this->adapter->read('3'));

        sleep(2);

        static::assertTrue($this->adapter->gc(1));

        // decrease 3 files
        static::assertEquals($this->getCountOfSessionFiles(), $countOfSessionFiles);

        static::assertEquals('', $this->adapter->read('1'));
        static::assertEquals('', $this->adapter->read('2'));
        static::assertEquals('', $this->adapter->read('3'));
    }

    /**
     * @return int
     */
    abstract protected function getCountOfSessionFiles();
}
