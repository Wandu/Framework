<?php
namespace Wandu\DI\Exception;

class NullReferenceException extends DIException
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
