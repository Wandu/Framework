<?php
namespace Wandu\Http\Session;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Http\Cookie\Cookie;
use Wandu\Http\Cookie\CookieJar;

class SessionFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateEmptySessionFromCookieJar()
    {
        $mockery = Mockery::mock(\SessionHandlerInterface::class);
        $mockery->shouldReceive('read')
            ->once()
            ->andReturn(''); // nothing
        
        $factory = new SessionFactory($mockery);
        
        $cookieJar = new CookieJar();
        
        $session = $factory->fromCookieJar($cookieJar);
        $this->assertEquals([], $session->getRawParams());
    }

    public function testCreateSessionFromCookieJar()
    {
        $mockery = Mockery::mock(\SessionHandlerInterface::class);
        $mockery->shouldReceive('read')
            ->once()
            ->with('wdsess_id1234')
            ->andReturn(serialize(['hello' => 'world'])); // nothing

        $factory = new SessionFactory($mockery);

        $cookieJar = new CookieJar([
            'WdSessId' => 'wdsess_id1234',
        ]);

        $session = $factory->fromCookieJar($cookieJar);
        $this->assertEquals(['hello' => 'world'], $session->getRawParams());
    }

    public function testCreateSessionFromCookieJarAndOtherSessionName()
    {
        $mockery = Mockery::mock(\SessionHandlerInterface::class);
        $mockery->shouldReceive('read')
            ->once()
            ->with('wdsess_id5678')
            ->andReturn(serialize(['hello' => 'world'])); // nothing

        $factory = new SessionFactory($mockery, [
            'name' => 'WanduSessionId'
        ]);

        $cookieJar = new CookieJar([
            'WanduSessionId' => 'wdsess_id5678',
        ]);

        $session = $factory->fromCookieJar($cookieJar);
        $this->assertEquals(['hello' => 'world'], $session->getRawParams());
    }

    public function testTo()
    {
        $mockery = Mockery::mock(\SessionHandlerInterface::class);
        $mockery->shouldReceive('write')
            ->once()
            ->with('session_id', serialize([
                'user' => [
                    'id' => 317,
                    'name' => 'wandu'
                ]
            ])); // nothing
        $mockery->shouldReceive('gc')->once();

        $factory = new SessionFactory($mockery, [
            'gc_frequency' => 1 // 1/1 => 100%
        ]);

        $cookieJar = new CookieJar();

        $factory->toCookieJar(new Session('session_id', [
            'user' => [
                'id' => 317,
                'name' => 'wandu'
            ]
        ]), $cookieJar);
        
        $this->assertEquals('session_id', $cookieJar->get('WdSessId'));

        // exists in setCookies
        $setCookies = iterator_to_array($cookieJar->getIterator());
        $this->assertInstanceOf(Cookie::class, $setCookies['WdSessId']);
    }
}
