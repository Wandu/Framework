<?php
namespace Wandu\Tempy\Lexer;

use RuntimeException;

class CannotFindTokenException extends RuntimeException
{
    /** @var string */
    protected $word;

    /**
     * @param string $word
     */
    public function __construct($word)
    {
        $this->word = $word;
        $this->message = "cannot find token: \"{$word}\"";
    }

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }
}
