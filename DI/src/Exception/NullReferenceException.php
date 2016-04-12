<?php
namespace Wandu\DI\Exception;

use Interop\Container\Exception\NotFoundException;

class NullReferenceException extends DIException implements NotFoundException
{
    /**
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
        $this->message = "It was not found; {$class}";
    }
}
