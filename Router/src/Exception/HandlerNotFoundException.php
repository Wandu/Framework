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
     * @param string $className
     * @param string $methodName
     */
    public function __construct($className, $methodName = null)
    {
        $this->className = $className;
        if (isset($methodName)) {
            $this->methodName = $methodName;
            parent::__construct("\"{$className}::{$methodName}\" is not found.");
        } else {
            parent::__construct("\"{$className}\" is not found.");
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
