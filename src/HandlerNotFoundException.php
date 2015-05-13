<?php
namespace Jicjjang\June;

use Exception;
use RuntimeException;

class HandlerNotFoundException extends RuntimeException
{
    public function __construct($message = "Handler not found.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
