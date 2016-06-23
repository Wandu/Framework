<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Mockery;

class UriExtendTest extends PHPUnit_Framework_TestCase
{
    public function urlProvider()
    {
        return [
            // default
            [
                'http://wani.kr',
                '/move-to-page',
                'http://wani.kr/move-to-page'
            ],

            // fragment set
            [
                'http://wani.kr?query=query#fragment',
                '/move-to-page',
                'http://wani.kr/move-to-page'
            ],
            [
                'http://wani.kr',
                '/move-to-page?query=query#fragment',
                'http://wani.kr/move-to-page?query=query#fragment'
            ],
            [
                'http://wani.kr?query=other#theotherfragment',
                '/move-to-page?query=query#fragment',
                'http://wani.kr/move-to-page?query=query#fragment'
            ],

            // other target
            [
                'http://wani.kr',
                '../../../move-to-page',
                'http://wani.kr/move-to-page'
            ],
            [
                'http://wani.kr',
                './move-to-page',
                'http://wani.kr/move-to-page'
            ],

            // file + target
            [
                'http://wani.kr/current/next/and-next',
                '/front/login/page',
                'http://wani.kr/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                'front/login/page',
                'http://wani.kr/current/next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                './front/login/page',
                'http://wani.kr/current/next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '../front/login/page',
                'http://wani.kr/current/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '../front/../page',
                'http://wani.kr/current/page'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '',
                'http://wani.kr/current/next/and-next'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '.',
                'http://wani.kr/current/next/and-next'
            ],
            [
                'http://wani.kr/current/next/and-next',
                '/',
                'http://wani.kr'
            ],

            // directory + target
            [
                'http://wani.kr/current/next/and-next/',
                '/front/login/page',
                'http://wani.kr/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                'front/login/page',
                'http://wani.kr/current/next/and-next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                './front/login/page',
                'http://wani.kr/current/next/and-next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                '../front/login/page',
                'http://wani.kr/current/next/front/login/page'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                '',
                'http://wani.kr/current/next/and-next/'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                '.',
                'http://wani.kr/current/next/and-next/'
            ],
            [
                'http://wani.kr/current/next/and-next/',
                '/',
                'http://wani.kr'
            ],
        ];
    }

    /**
     * @dataProvider urlProvider
     */
    public function testJoin($base, $target, $expected)
    {
        $this->assertSame($expected, (new Uri($base))->join(new Uri($target))->__toString());
    }

    public function testGetQueryParams()
    {
        $url = new Uri('/hello?foo=30&bar=&baz');
        $this->assertEquals('/hello?foo=30&bar=&baz', $url->__toString());

        $this->assertTrue($url->hasQueryParam('foo'));
        $this->assertFalse($url->hasQueryParam('bar'));
        $this->assertFalse($url->hasQueryParam('baz'));
        $this->assertFalse($url->hasQueryParam('qux'));

        $this->assertSame('30', $url->getQueryParam('foo'));
        $this->assertSame(null, $url->getQueryParam('bar'));
        $this->assertSame(null, $url->getQueryParam('baz'));
        $this->assertSame(null, $url->getQueryParam('qux'));

        $this->assertSame('30', $url->getQueryParam('foo', 'default'));
        $this->assertSame('default', $url->getQueryParam('bar', 'default'));
        $this->assertSame('default', $url->getQueryParam('baz', 'default'));
        $this->assertSame('default', $url->getQueryParam('qux', 'default'));
    }

    public function testGetQueryParamsStrict()
    {
        $url = new Uri('/hello?foo=30&bar=&baz', true);
        $this->assertEquals('/hello?foo=30&bar=&baz', $url->__toString());

        $this->assertTrue($url->hasQueryParam('foo'));
        $this->assertTrue($url->hasQueryParam('bar'));
        $this->assertTrue($url->hasQueryParam('baz'));
        $this->assertFalse($url->hasQueryParam('qux'));

        $this->assertSame('30', $url->getQueryParam('foo'));
        $this->assertSame('', $url->getQueryParam('bar'));
        $this->assertSame('', $url->getQueryParam('baz'));
        $this->assertSame(null, $url->getQueryParam('qux'));

        $this->assertSame('30', $url->getQueryParam('foo', 'default'));
        $this->assertSame('', $url->getQueryParam('bar', 'default'));
        $this->assertSame('', $url->getQueryParam('baz', 'default'));
        $this->assertSame('default', $url->getQueryParam('qux', 'default'));
    }

    public function testWithQueryParams()
    {
        $url = new Uri('/hello?foo=30&bar=&baz');

        $this->assertEquals('/hello?foo=30&bar=&baz', $url->__toString());

        $this->assertEquals('/hello?foo=40', $url->withQueryParam('foo', 40)->__toString());
        $this->assertEquals('/hello?foo=kkk+hhh', $url->withQueryParam('foo', 'kkk hhh')->__toString());
        $this->assertEquals('/hello', $url->withQueryParam('foo', null)->__toString());

        $this->assertEquals('/hello', $url->withoutQueryParam('foo')->__toString());
        $this->assertEquals('/hello?foo=30', $url->withoutQueryParam('bar')->__toString());
    }

    public function testWithQueryParamsStrict()
    {
        $url = new Uri('/hello?foo=30&bar=&baz', true);

        $this->assertEquals('/hello?foo=30&bar=&baz', $url->__toString());

        $this->assertEquals('/hello?foo=40&bar=&baz=', $url->withQueryParam('foo', 40)->__toString());
        $this->assertEquals('/hello?bar=&baz=', $url->withQueryParam('foo', null)->__toString());

        $this->assertEquals(
            '/hello?foo=30&bar=&baz=&qux=40&quux=50',
            $url->withQueryParam('qux', 40)->withQueryParam('quux', 50)->__toString()
        );

        $this->assertEquals('/hello?bar=&baz=', $url->withoutQueryParam('foo')->__toString());
        $this->assertEquals('/hello?foo=30&baz=', $url->withoutQueryParam('bar')->__toString());
    }
}
