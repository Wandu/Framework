<?php
namespace Wandu\DI;

use Exception;
use RuntimeException;

class CannotChangeException extends RuntimeException
{
    public function __construct(
        $message = null,
        $code = 0,
        Exception $previous = null
    ) {
        if (!isset($message)) {
            $message = 'You cannot change the data.';
        } else {
            $message = 'You cannot change the data; ' . $message;
        }
        parent::__construct($message, $code, $previous);
    }
}
