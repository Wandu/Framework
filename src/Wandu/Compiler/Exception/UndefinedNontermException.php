<?php
namespace Wandu\Compiler\Exception;

use RuntimeException;

class UndefinedNontermException extends RuntimeException
{
    /** @var string */
    protected $nonterm;

    public function __construct($nonterm)
    {
        $this->nonterm = $nonterm;
        $this->message = "undefined nonterminal token.";
    }

    /**
     * @return string
     */
    public function getUndefinedNonterm()
    {
        return $this->nonterm;
    }
}
