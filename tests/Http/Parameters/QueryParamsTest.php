<?php
namespace Wandu\Http\Parameters;

use Mockery;
use Psr\Http\Message\ServerRequestInterface;

class QueryParamsTest extends ParameterTest
{
    public function setUp()
    {
        $this->param1 = new QueryParams($this->createRequest($this->param1Attributes));
        $this->param2 = new QueryParams($this->createRequest($this->param2Attributes));
        $this->param3 = new QueryParams($this->createRequest($this->param3Attributes), new Parameter($this->param3FallbackAttributes));
    }

    /**
     * @param array $attributes
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function createRequest(array $attributes = [])
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getQueryParams')->andReturn($attributes);
        return $request;
    }
}
