<?php
namespace Wandu\DI;

use Exception;
use RuntimeException;

class CannotChangeException extends RuntimeException
{
    public function __construct($message = 'You cannot change the data.', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
