<?php
namespace Wandu\Http\Session\Handler;

use Wandu\Http\Session\HandlerTestCase;

class FileHandlerTest extends HandlerTestCase
{
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

    /**
     * {@inheritdoc}
     */
    protected function getCountOfSessionFiles()
    {
        return iterator_count(new \DirectoryIterator(__DIR__ . '/sessions'));
    }
}
