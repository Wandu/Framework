<?php
namespace Wandu\DI\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class CannotChangeException extends RuntimeException implements ContainerExceptionInterface
{
    /** @var string */
    protected $class;

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
        $this->message = "it cannot be changed; \"{$class}\".";
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
