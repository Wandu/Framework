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
        static::assertSame($expected, (new Uri($base))->join(new Uri($target))->__toString());
    }

    public function testGetQueryParams()
    {
        $url = new Uri('/hello?foo=30&bar=&baz');
        static::assertEquals('/hello?foo=30&bar=&baz', $url->__toString());

        static::assertTrue($url->hasQueryParam('foo'));
        static::assertFalse($url->hasQueryParam('bar'));
        static::assertFalse($url->hasQueryParam('baz'));
        static::assertFalse($url->hasQueryParam('qux'));

        static::assertSame('30', $url->getQueryParam('foo'));
        static::assertSame(null, $url->getQueryParam('bar'));
        static::assertSame(null, $url->getQueryParam('baz'));
        static::assertSame(null, $url->getQueryParam('qux'));

        static::assertSame('30', $url->getQueryParam('foo', 'default'));
        static::assertSame('default', $url->getQueryParam('bar', 'default'));
        static::assertSame('default', $url->getQueryParam('baz', 'default'));
        static::assertSame('default', $url->getQueryParam('qux', 'default'));
    }

    public function testGetQueryParamsStrict()
    {
        $url = new Uri('/hello?foo=30&bar=&baz');
        static::assertEquals('/hello?foo=30&bar=&baz', $url->__toString());

        static::assertTrue($url->hasQueryParam('foo', true));
        static::assertTrue($url->hasQueryParam('bar', true));
        static::assertTrue($url->hasQueryParam('baz', true));
        static::assertFalse($url->hasQueryParam('qux', true));

        static::assertSame('30', $url->getQueryParam('foo', null, true));
        static::assertSame('', $url->getQueryParam('bar', null, true));
        static::assertSame('', $url->getQueryParam('baz', null, true));
        static::assertSame(null, $url->getQueryParam('qux', null, true));

        static::assertSame('30', $url->getQueryParam('foo', 'default', true));
        static::assertSame('', $url->getQueryParam('bar', 'default', true));
        static::assertSame('', $url->getQueryParam('baz', 'default', true));
        static::assertSame('default', $url->getQueryParam('qux', 'default', true));
    }

    public function testWithQueryParams()
    {
        $url = new Uri('/hello?foo=30&bar=&baz');

        static::assertEquals('/hello?foo=30&bar=&baz', $url->__toString());

        static::assertEquals('/hello?foo=40&bar=&baz=', $url->withQueryParam('foo', 40)->__toString());
        static::assertEquals('/hello?foo=kkk+hhh&bar=&baz=', $url->withQueryParam('foo', 'kkk hhh')->__toString());
        static::assertEquals('/hello?bar=&baz=', $url->withQueryParam('foo', null)->__toString());

        static::assertEquals('/hello?bar=&baz=', $url->withoutQueryParam('foo')->__toString());
        static::assertEquals('/hello?foo=30&baz=', $url->withoutQueryParam('bar')->__toString());

        $url = new Uri('/hello?foo=30&bar=&baz');

        static::assertEquals('/hello?foo=30&bar=&baz', $url->__toString());

        static::assertEquals('/hello?foo=40&bar=&baz=', $url->withQueryParam('foo', 40)->__toString());
        static::assertEquals('/hello?bar=&baz=', $url->withQueryParam('foo', null)->__toString());

        static::assertEquals(
            '/hello?foo=30&bar=&baz=&qux=40&quux=50',
            $url->withQueryParam('qux', 40)->withQueryParam('quux', 50)->__toString()
        );

        static::assertEquals('/hello?bar=&baz=', $url->withoutQueryParam('foo')->__toString());
        static::assertEquals('/hello?foo=30&baz=', $url->withoutQueryParam('bar')->__toString());
    }
}
