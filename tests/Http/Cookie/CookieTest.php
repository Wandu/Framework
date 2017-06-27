<?php
namespace Wandu\Http\Cookie;

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CookieTest extends TestCase
{
    public function testConstruct()
    {
        try {
            new Cookie('');
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('The cookie name cannot be empty.', $e->getMessage());
        }
        try {
            new Cookie(',');
            static::fail();
        } catch (InvalidArgumentException $e) {
            static::assertEquals('The cookie name "," contains invalid characters.', $e->getMessage());
        }
    }

    public function testDeleteCookie()
    {
        static::assertEquals(
            "hello=deleted; Expires=Thursday, 01-Jan-1970 00:00:00 GMT; Max-Age=0; Path=/; HttpOnly",
            (new Cookie('hello'))->__toString()
        );
    }

    public function testSetCookie()
    {
        static::assertEquals(
            "hello=world; Path=/; HttpOnly",
            (new Cookie('hello', 'world'))->__toString()
        );
    }

    public function testSetCookieWithExpireTime()
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp(10);
        static::assertEquals(
            "hello=world; Expires=Thursday, 01-Jan-1970 00:00:10 GMT; Max-Age=-1; Path=/; HttpOnly",
            (new Cookie('hello', 'world', $dateTime))->__toString()
        );
    }

    public function testSetCookieWithMeta()
    {
        static::assertEquals(
            "hello=world; Path=/hello; Domain=blog.wani.kr; Secure",
            (new Cookie('hello', 'world', null, '/hello', 'blog.wani.kr', true, false))->__toString()
        );
    }

    public function testGetMetaData()
    {
        $cookie = new Cookie('hello', 'world', null, '/hello', 'blog.wani.kr', true, false);

        static::assertEquals('hello', $cookie->getName());
        static::assertEquals('world', $cookie->getValue());
        static::assertNull($cookie->getExpire());
        static::assertEquals('/hello', $cookie->getPath());
        static::assertEquals('blog.wani.kr', $cookie->getDomain());

        static::assertTrue($cookie->isSecure());
        static::assertFalse($cookie->isHttpOnly());
    }
}
