<?php
namespace Wandu\Http\Session\Handler;

use Wandu\Http\Session\HandlerTestCase;

class GlobalHandlerTest extends HandlerTestCase
{
    public function setUp()
    {
        $this->adapter = new GlobalHandler();
    }

    public function testMultiIdSession()
    {
        static::addToAssertionCount(1); // do nothing
    }

    public function testGarbageCollection()
    {
        static::addToAssertionCount(1); // do nothing
    }
    
    protected function getCountOfSessionFiles()
    {
        // do nothing
        return 0;
    }
}
