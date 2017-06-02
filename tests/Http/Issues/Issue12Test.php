<?php
namespace Wandu\Http\Issues;

use Mockery;
use PHPUnit\Framework\TestCase;
use Wandu\Http\Psr\ServerRequest;

class Issue12Test extends TestCase
{
    public function testGetAttribute()
    {
        $request = (new ServerRequest())->withAttribute('null', null);

        static::assertNull($request->getAttribute('null'));
        static::assertNull($request->getAttribute('null', 20));
    }
}
