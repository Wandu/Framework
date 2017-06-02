<?php
namespace Wandu\Http\Psr;

use Mockery;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
     use MessageTestTrait;

    public function setUp()
    {
        $this->message = new Message(null, [], "1.0");
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
