<?php
namespace Wandu\DI\Exception;

use Interop\Container\Exception\NotFoundException;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NullReferenceException extends RuntimeException implements NotFoundExceptionInterface, NotFoundException
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
}
