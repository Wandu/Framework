<?php
namespace Wandu\Compiler;

use Mockery;
use PHPUnit_Framework_TestCase;

class LexicalAnalyzerTest extends PHPUnit_Framework_TestCase
{
    public function testAnalyze()
    {
        $result = '';
        $lexer = new LexicalAnalyzer([
            '\\+' => function () {
                return 't_add';
            },
            '\\-' => function () {
                return 't_minus';
            },
            '\\*' => function () {
                return 't_multi';
            },
            '\\/' => function () {
                return 't_divide';
            },
            '\\=' => function () {
                return 't_equal';
            },
            '[1-9][0-9]*|0([0-7]+|(x|X)[0-9A-Fa-f]*)?' => function ($word) use (&$result) {
                $result .= $word;
                return "t_number";
            },
            '\s' => null,
        ]);

        $this->assertEquals([
            't_number', 't_add', 't_number', 't_equal', 't_number',
        ], $lexer->analyze('10 + 20 = 0'));

        $this->assertEquals('10200', $result);
    }
}
