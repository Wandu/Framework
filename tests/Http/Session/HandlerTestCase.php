<?php
namespace Wandu\Http\Session;

use PHPUnit_Framework_TestCase;

abstract class HandlerTestCase extends PHPUnit_Framework_TestCase
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

        $this->assertEquals('', $session);
    }
    
    public function testNothingToDestroy()
    {
        $sessionId = sha1(uniqid());
        $this->adapter->destroy($sessionId);
    }

    public function testMultiIdSession()
    {
        $sessionId1 = sha1(uniqid());
        $sessionId2 = sha1(uniqid());

        // write
        $this->adapter->write($sessionId1, serialize([
            'name' => 'multi session test',
        ]));

        $this->assertEquals([
            'name' => 'multi session test',
        ], unserialize($this->adapter->read($sessionId1)));
        $this->assertEquals('', $this->adapter->read($sessionId2));

        // destroy
        $this->adapter->destroy($sessionId2);

        $this->assertEquals([
            'name' => 'multi session test',
        ], unserialize($this->adapter->read($sessionId1)));
        $this->assertEquals('', $this->adapter->read($sessionId2));

        $this->adapter->destroy($sessionId1);

        // then blank
        $this->assertEquals('', $this->adapter->read($sessionId1));
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
        $this->assertEquals([
            'hello' => 'world',
            'what' => 'um..'
        ], unserialize($this->adapter->read($sessionId)));

        // destroy
        $this->adapter->destroy($sessionId);

        // then blank
        $this->assertEquals('', $this->adapter->read($sessionId));
    }

    public function testGarbageCollection()
    {
        $countOfSessionFiles = $this->getCountOfSessionFiles();

        $this->adapter->write('1', 'string 1');
        $this->adapter->write('2', 'string 2');
        $this->adapter->write('3', 'string 3');

        // increase 3 files
        $this->assertEquals($this->getCountOfSessionFiles(), $countOfSessionFiles + 3);

        $this->assertTrue($this->adapter->gc(1));

        $this->assertEquals('string 1', $this->adapter->read('1'));
        $this->assertEquals('string 2', $this->adapter->read('2'));
        $this->assertEquals('string 3', $this->adapter->read('3'));

        sleep(2);

        $this->assertTrue($this->adapter->gc(1));

        // decrease 3 files
        $this->assertEquals($this->getCountOfSessionFiles(), $countOfSessionFiles);

        $this->assertEquals('', $this->adapter->read('1'));
        $this->assertEquals('', $this->adapter->read('2'));
        $this->assertEquals('', $this->adapter->read('3'));
    }

    /**
     * @return int
     */
    abstract protected function getCountOfSessionFiles();
}
