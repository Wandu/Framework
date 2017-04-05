<?php
namespace Wandu\Http\Parameters;

use Mockery;
use Psr\Http\Message\ServerRequestInterface;

class ParsedBodyTest extends ParameterTest
{
    public function setUp()
    {
        $this->param1 = new ParsedBody($this->createRequest($this->param1Attributes));
        $this->param2 = new ParsedBody($this->createRequest($this->param2Attributes));
        $this->param3 = new ParsedBody($this->createRequest($this->param3Attributes), new Parameter($this->param3FallbackAttributes));
    }

    /**
     * @param array $attributes
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function createRequest(array $attributes = [])
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')->andReturn($attributes);
        return $request;
    }
}
