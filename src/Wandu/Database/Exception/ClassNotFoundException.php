<?php
namespace Wandu\Database\Exception;

use RuntimeException;

class ClassNotFoundException extends RuntimeException
{
    /** @var string */
    protected $className;
    
    public function __construct($className)
    {
        $this->className = $className;
        $this->message = "Class '{$className}' not found in {$this->getFile()} code on line {$this->getLine()}";
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
