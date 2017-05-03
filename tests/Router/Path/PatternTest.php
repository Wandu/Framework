<?php
namespace Wandu\Router\Path;

use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\Router\Exception\CannotGetPathException;

/**
 * path parser support rules from path-to-regexp of npm.
 * but, no support "zero or more" and "one or more".
 *
 * @ref https://github.com/pillarjs/path-to-regexp
 */
class PatternTest extends TestCase 
{
    use Assertions;
    
    public function testSimple()
    {
        $pattern = new Pattern(":foo");
        static::assertSame([
            [
                ['foo', '[^/]+'],
            ]
        ], $pattern->parse());
        static::assertSame('30', $pattern->path(['foo' => 30]));
        static::assertSame('hello%20world', $pattern->path(['foo' => "hello world"]));

        $pattern = new Pattern("foo");
        static::assertSame([
            [
                'foo',
            ],
        ], $pattern->parse());
        static::assertSame('foo?foo=30', $pattern->path(['foo' => 30]));
        static::assertSame('foo?foo=hello%20world', $pattern->path(['foo' => "hello world"]));
    }
    
    public function testNamedParams()
    {
        $pattern = new Pattern("/:foo/:bar"); // safe => '/test/route', ['foo' => test, 'bar' => 'route']

        static::assertSame([
            [
                '/',
                ['foo', '[^/]+'],
                '/',
                ['bar', '[^/]+'],
            ],
        ], $pattern->parse());
        static::assertSame('/1111/2222', $pattern->path(['foo' => 1111, 'bar' => 2222]));
        static::assertException(new CannotGetPathException(['foo', 'bar']), function () use ($pattern) {
            $pattern->path(['foo' => 1111]);
        });
        static::assertException(new CannotGetPathException(['foo', 'bar']), function () use ($pattern) {
            $pattern->path(['bar' => 1111]);
        });
        static::assertException(new CannotGetPathException(['foo', 'bar']), function () use ($pattern) {
            $pattern->path();
        });

        $pattern = new Pattern('/(apple-)?icon-:res(\d+).png'); // safe => '/icon-76.png', ['res => 76]
        static::assertSame([
            [
                ['', '(?:\/apple-)?'],
                'icon-',
                ['res', '\d+'],
                '.png',
            ],
        ], $pattern->parse());
        static::assertSame('/icon-30.png', $pattern->path(['res' => 30]));
        static::assertException(new CannotGetPathException(['res']), function () use ($pattern) {
            $pattern->path(['foo' => 1111]);
        });
    }
    
    public function testOptionalParams()
    {
        $pattern = new Pattern("/:foo/:bar?");
        static::assertSame([
            [
                '/',
                ['foo', '[^/]+'],
            ],
            [
                '/',
                ['foo', '[^/]+'],
                '/',
                ['bar', '[^/]+'],
            ],
        ], $pattern->parse());
        static::assertSame('/1111', $pattern->path(['foo' => 1111]));
        static::assertSame('/1111/2222', $pattern->path(['foo' => 1111, 'bar' => 2222]));
    }
    
    public function testCustomMatchParams()
    {
        $pattern = new Pattern('/:foo(\d+)');

        static::assertSame([
            [
                '/',
                ['foo', '\d+'],
            ],
        ], $pattern->parse());
    }
    
    public function testUnnamedParams()
    {
        $pattern = new Pattern('/:foo/(.*)');

        static::assertSame([
            [
                '/',
                ['foo', '[^/]+'],
                ['', '(?:\/.*)'],
            ],
        ], $pattern->parse());
        static::assertSame('/1111', $pattern->path(['foo' => 1111]));
    }
    
    public function testAsterisk()
    {
        $pattern = new Pattern('/foo/*');

        static::assertSame([
            [
                '/foo',
                ['', '\/.*'],
            ],
        ], $pattern->parse());
        static::assertSame('/foo', $pattern->path());

        $pattern = new Pattern('/foo-*');

        static::assertSame([
            [
                '/foo-',
                ['', '.*'],
            ],
        ], $pattern->parse());
        static::assertSame('/foo-', $pattern->path());
    }
}
