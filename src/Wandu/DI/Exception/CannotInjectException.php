<?php
namespace Wandu\DI\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class CannotInjectException extends RuntimeException implements ContainerExceptionInterface 
{
    /** @var string */
    protected $class;

    /** @var string */
    protected $property;
    
    /**
     * @param string $class
     * @param string $property
     */
    public function __construct($class, $property)
    {
        $this->class = $class;
        $this->property = $property;
        $this->message = "it cannot be injected; {$class}::\${$property}";
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
    
    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
