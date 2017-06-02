<?php
namespace Wandu\Http\Psr;

use PHPUnit\Framework\TestCase;
use Mockery;

class ResponseTest extends TestCase
{
    use ResponseTestTrait, MessageTestTrait;

    public function setUp()
    {
        $this->response = $this->message = new Response();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
