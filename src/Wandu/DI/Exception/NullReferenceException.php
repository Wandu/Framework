<?php
namespace Wandu\DI\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NullReferenceException extends RuntimeException implements NotFoundExceptionInterface
{
    /** @var string */
    protected $class;
    
    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
        $this->message = "it was not found; \"{$class}\".";
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
