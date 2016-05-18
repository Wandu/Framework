<?php
namespace Wandu\Router\Exception;

use RuntimeException;

class HandlerNotFoundException extends RuntimeException
{
    /** @var string */
    protected $className;

    /** @var string */
    protected $methodName;

    /**
     * @param string $classParameter
     * @param string $methodName
     */
    public function __construct($classParameter, $methodName = null)
    {
        $this->className = $classParameter;
        if (isset($methodName)) {
            $this->methodName = $methodName;
            parent::__construct("\"{$classParameter}::{$methodName}\" is not found.");
        } else {
            parent::__construct("\"{$classParameter}\" is not found.");
        }
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }
}
