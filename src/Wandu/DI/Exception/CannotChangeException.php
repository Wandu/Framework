<?php
namespace Wandu\DI\Exception;

class CannotChangeException extends DIException
{
    /**
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
        $this->message = "It cannot be changed; {$class}";
    }
}
