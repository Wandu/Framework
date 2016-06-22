<?php
namespace Wandu\Http\Parameters;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;

class QueryParamsTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstructor()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')->andReturn([]);

        $parsedBody = new QueryParams($request);

        $this->assertEquals([], $parsedBody->toArray());
    }
}
