<?php
namespace Wandu\DI;

use Exception;
use RuntimeException;

class CannotResolveException extends RuntimeException
{
    public function __construct(
        $message = null,
        $code = 0,
        Exception $previous = null
    ) {
        if (!isset($message)) {
            $message = 'Auto resolver can resolve the class that use params with type hint.';
        } else {
            $message = 'Auto resolver can resolve the class that use params with type hint; ' . $message;
        }
        parent::__construct($message, $code, $previous);
    }
}
