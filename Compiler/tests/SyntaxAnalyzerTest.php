<?php
namespace Wandu\Compiler;

use Mockery;
use PHPUnit_Framework_TestCase;

class SyntaxAnalyzerTest extends PHPUnit_Framework_TestCase
{
    public function testAnalyze()
    {
        $syntaxer = new SyntaxAnalyzer(new LexicalAnalyzer([
            '\\+' => function () {
                return 'T_ADD';
            },
            '\\-' => function () {
                return 'T_MINUS';
            },
            '\\*' => function () {
                return 'T_MULTI';
            },
            '\\/' => function () {
                return 'T_DIV';
            },
            '\\=' => function () {
                return 'T_EQUAL';
            },
            '[1-9][0-9]*|0([0-7]+|(x|X)[0-9A-Fa-f]*)?' => function ($word) {
                return new Token("T_NUMBER", $word);
            },
            '\s' => null,
        ]));

        $syntaxer->addToken(['T_ADD', 'T_MINUS', 'T_MULTI', 'T_DIV', 'T_EQUAL']);

        $syntaxer->setRootSyntax('root');
        $syntaxer->addSyntax('root', ['formula'], function ($x) {
            return $x;
        });
        $syntaxer->addSyntax('formula', ['T_NUMBER', 'T_MINUS', 'T_NUMBER'], function ($x, $_, $y) {
            return $x - $y;
        });
        $syntaxer->addSyntax('formula', ['T_NUMBER', 'T_MINUS', 'T_NUMBER'], function ($x, $_, $y) {
            return $x - $y;
        });
        $syntaxer->addSyntax('formula', ['T_NUMBER', 'T_ADD', 'T_NUMBER'], function ($x, $_, $y) {
            return $x + $y;
        });
        $syntaxer->addSyntax('formula', ['T_NUMBER', 'T_MULTI', 'T_NUMBER'], function ($x, $_, $y) {
            return $x * $y;
        });
        $syntaxer->addSyntax('formula', ['T_NUMBER', 'T_DIV', 'T_NUMBER'], function ($x, $_, $y) {
            return $x / $y;
        });

        $this->assertEquals(30, $syntaxer->analyze('10 + 20'));
    }
}
