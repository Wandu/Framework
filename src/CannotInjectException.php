<?php
namespace Wandu\DI;

use RuntimeException;

class CannotInjectException extends RuntimeException
{
    /**
     * @param string $className
     * @param string $propertyName
     */
    public function __construct($className, $propertyName)
    {
        parent::__construct("Cannot inject; {$className}::\${$propertyName}");
    }
}
