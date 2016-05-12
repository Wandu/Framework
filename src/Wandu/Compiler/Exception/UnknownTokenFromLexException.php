<?php
namespace Wandu\Compiler\Exception;

use RuntimeException;

class UnknownTokenFromLexException extends RuntimeException
{
    /** @var string */
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
        $this->message = "unknown token \"{$token}\".";
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
