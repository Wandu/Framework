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
    public function __construct($className, $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        parent::__construct("\"{$className}::{$methodName}\" is not found.");
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
