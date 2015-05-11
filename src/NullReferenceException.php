<?php
namespace Wandu\DI;

use Exception;
use RuntimeException;

class NullReferenceException extends RuntimeException
{
    public function __construct(
        $message = 'You cannot access null reference container.',
        $code = 0,
        Exception $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
