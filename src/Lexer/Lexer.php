<?php
namespace Wandu\Tempy\Lexer;

class Lexer
{
    /** @var array */
    protected static $tokens = [
        Token::T_WHITESPACE => '\s',
        Token::T_OPEN_BRACKET => '\{\{',
        Token::T_CLOSE_BRACKET => '\}\}',
        Token::T_VARIABLE => '\$[a-zA-Z_][a-zA-Z0-9_]*',
        Token::T_NEWLINE => '\n',
        Token::T_TEXT => '.',
    ];

    protected function getTokensRegEx()
    {
        $result = [];
        for ($i = 0, $len = max(array_keys(static::$tokens)); $i <= $len; $i++) {
            $result[] = isset(static::$tokens[$i]) ? static::$tokens[$i] : '';
        }
        return '%(' . implode(')|(', $result). ')%';
    }

    public function analyze($code)
    {
        $tokens = [];

        preg_match_all($this->getTokensRegEx(), $code, $matches);
        $words= array_shift($matches);

        $isOpenBracket = false;
        $textBuffer = '';

        foreach ($words as $idx => $word) {
            $tokenNumber = $this->findTokenNumber($matches, $idx, $word);
            if ($isOpenBracket) {
                switch ($tokenNumber) {
                    case Token::T_WHITESPACE:
                        break;
                    case Token::T_VARIABLE :
                        $tokens[] = [Token::T_VARIABLE, $word];
                        break;
                    case Token::T_CLOSE_BRACKET : // not need break.
                        $isOpenBracket = false;
                        $tokens[] = [Token::T_CLOSE_BRACKET];
                        break;
                    default:
                        $tokens[] = [$tokenNumber];
                }
            } else {
                if ($tokenNumber === Token::T_OPEN_BRACKET) {
                    if ($textBuffer) {
                        $tokens[] = [Token::T_TEXT, $textBuffer];
                        $textBuffer = '';
                    }
                    $isOpenBracket = true;
                    $tokens[] = [$tokenNumber];
                } else {
                    $textBuffer .= $word;
                }
            }
        }
        if (!$isOpenBracket && $textBuffer) {
            $tokens[] = [Token::T_TEXT, $textBuffer];
        }
        return $tokens;
    }

    protected function findTokenNumber($matches, $idx, $word) {
        foreach ($matches as $number => $match) {
            if (isset($match[$idx]) && $match[$idx] !== '') {
                return $number;
            }
        }
        throw new CannotFindTokenException($word);
    }
}
