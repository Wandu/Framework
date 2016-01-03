<?php
namespace Wandu\Tempy;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Tempy\Lexer\Lexer;
use Wandu\Tempy\Lexer\Token;

class LexerTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Tempy\Lexer\Lexer */
    protected $lexer;

    public function setUp()
    {
        $this->lexer = new Lexer();
    }

    /**
     * @dataProvider provider
     */
    public function testParse($inputFile, $outputTokens)
    {
        $input = trim(file_get_contents(__DIR__ . '/input/' . $inputFile));
        $this->assertEquals($outputTokens, $this->lexer->analyze($input));
    }

    public function provider()
    {
        return [
            ['variable-as-variable.php', [
                [Token::T_TEXT, 'Hello, '],
                [Token::T_OPEN_BRACKET],
                [Token::T_VARIABLE, '$target'],
                [Token::T_CLOSE_BRACKET],
                [Token::T_TEXT, '!'],
            ]],
        ];
    }
}
