<?php
namespace Wandu\DI;

use Exception;
use RuntimeException;

class NullReferenceException extends RuntimeException
{
    public function __construct(
        $message = null,
        $code = 0,
        Exception $previous = null
    ) {
        if (!isset($message)) {
            $message = 'You cannot access null reference container.';
        } else {
            $message = 'You cannot access null reference container; ' . $message;
        }
        parent::__construct($message, $code, $previous);
    }
}
