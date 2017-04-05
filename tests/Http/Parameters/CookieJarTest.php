<?php
namespace Wandu\Http\Parameters;

use Mockery;
use Psr\Http\Message\ServerRequestInterface;

class CookieJarTest extends ParameterTest
{
    use TestGetAndSet;
    
    /** @var \Wandu\Http\Contracts\CookieJarInterface */
    protected $param1;

    /** @var \Wandu\Http\Contracts\CookieJarInterface */
    protected $param2;

    /** @var \Wandu\Http\Contracts\CookieJarInterface */
    protected $param3;

    public function setUp()
    {
        $this->param1 = new CookieJar($this->createRequest($this->param1Attributes));
        $this->param2 = new CookieJar($this->createRequest($this->param2Attributes));
        $this->param3 = new CookieJar($this->createRequest($this->param3Attributes), new Parameter($this->param3FallbackAttributes));
    }
    
    /**
     * @param array $attributes
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function createRequest(array $attributes = [])
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getCookieParams')->andReturn($attributes);
        return $request;
    }
}
