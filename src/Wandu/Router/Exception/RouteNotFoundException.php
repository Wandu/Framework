<?php
namespace Wandu\Router\Exception;

use Exception;
use RuntimeException;

class RouteNotFoundException extends RuntimeException
{
    public function __construct($message = "Route not found.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
