<?php
namespace Wandu\DI\Exception;

use RuntimeException;

class CannotResolveException extends RuntimeException
{
    /** @var string */
    protected $class;

    /** @var string */
    protected $parameter;

    /**
     * @param string $class
     * @param string $parameter
     */
    public function __construct($class, $parameter)
    {
        $this->message = "cannot resolve the \"{$parameter}\" parameter in the \"{$class}\" class.";
        $this->class = $class;
        $this->parameter = $parameter;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}
