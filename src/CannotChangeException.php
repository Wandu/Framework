<?php
namespace Wandu\DI;

use Exception;
use RuntimeException;

class CannotChangeException extends RuntimeException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $message = 'You cannot change the data; ' . $message;
        parent::__construct($message, $code, $previous);
    }
}
