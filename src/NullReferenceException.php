<?php
namespace Wandu\DI;

use Exception;
use RuntimeException;

class NullReferenceException extends RuntimeException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $message = 'not exists in this container; ' . $message;
        parent::__construct($message, $code, $previous);
    }
}
