<?php
namespace Wandu\DI\Exception;

class CannotResolveException extends DIException
{
    /**
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
        $this->message = "It cannot be resolved; {$class}";
    }
}
