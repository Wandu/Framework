<?php
namespace Wandu\Compiler\Exception;

use RuntimeException;

class UnknownTokenException extends RuntimeException
{
    /** @var string */
    protected $remainContext;

    public function __construct($nonterm)
    {
        $this->remainContext = $nonterm;
        $this->message = "unknown token.";
    }

    /**
     * @return string
     */
    public function getRemainContext()
    {
        return $this->remainContext;
    }
}
