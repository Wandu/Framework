<?php
namespace Wandu\Http\Parameters;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ParsedBodyTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstructor()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')->andReturn([]);
        
        $parsedBody = new ParsedBody($request);
        
        $this->assertEquals([], $parsedBody->toArray());
    }
}
