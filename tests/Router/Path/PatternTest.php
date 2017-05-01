<?php
namespace Wandu\Router\Path;

use PHPUnit\Framework\TestCase;

/**
 * path parser support rules from path-to-regexp of npm.
 * but, no support "zero or more" and "one or more".
 *
 * @ref https://github.com/pillarjs/path-to-regexp
 */
class PatternTest extends TestCase 
{
    public function testSimple()
    {
        $pattern = new Pattern(":foo");
        static::assertSame([
            [
                ['foo', '[^/]+'],
            ]
        ], $pattern->parse());

        $pattern = new Pattern("foo");
        static::assertSame([
            [
                'foo',
            ],
        ], $pattern->parse());
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

        $pattern = new Pattern('/(apple-)?icon-:res(\d+).png'); // safe => '/icon-76.png', ['res => 76]
        static::assertSame([
            [
                '/',
                ['', '(?:apple-)?'],
                'icon-',
                ['res', '\d+'],
                '.png',
            ],
        ], $pattern->parse());
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
                '/',
                ['', '(?:.*)'],
            ],
        ], $pattern->parse());
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

        $pattern = new Pattern('/foo-*');

        static::assertSame([
            [
                '/foo-',
                ['', '.*'],
            ],
        ], $pattern->parse());
    }
}
