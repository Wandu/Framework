<?php
namespace Wandu\Http\Session\Handler;

use Wandu\Http\Session\HandlerTestCase;

/**
 * @runTestsInSeparateProcesses
 */
class GlobalHandlerTest extends HandlerTestCase
{
    public function setUp()
    {
        $this->adapter = new GlobalHandler();
    }

    public function testMultiIdSession()
    {
        // do nothing
    }

    public function testGarbageCollection()
    {
        // do nothing;
    }
    
    protected function getCountOfSessionFiles()
    {
        // do nothing
        return 0;
    }
}
