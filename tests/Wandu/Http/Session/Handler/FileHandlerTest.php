<?php
namespace Wandu\Http\Session\Handler;

use PHPUnit_Framework_TestCase;

class FileHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Session\Handler\FileHandler */
    protected $adapter;
    
    public function setUp()
    {
        if (!is_dir(__DIR__ . '/sessions')) {
            mkdir(__DIR__ . '/sessions');
        }
        $this->adapter = new FileHandler(__DIR__ . '/sessions');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->deleteAll(__DIR__ . '/sessions');
    }

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
        if (!isset($this->adapter)) {
            $this->markTestSkipped('there is no adapter! :-)');
        }
        $session = $this->adapter->read(sha1(uniqid()));

        $this->assertEquals('', $session);
    }

    public function testWriteSession()
    {
        if (!isset($this->adapter)) {
            $this->markTestSkipped('there is no adapter! :-)');
        }
        $sessionId = sha1(uniqid());

        // write
        $this->adapter->write($sessionId, serialize([
            'hello' => 'world',
            'what' => 'um..'
        ]));

        // then data}}
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
    
    protected function getCountOfSessionFiles()
    {
        return iterator_count(new \DirectoryIterator(__DIR__ . '/sessions'));
    }
}
