<?php
namespace Wandu\DI\Exception;

use Interop\Container\Exception\NotFoundException;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Wandu\Reflection\ReflectionCallable;

class CannotResolveException extends RuntimeException implements NotFoundExceptionInterface, NotFoundException
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
        if (is_string($class) && class_exists($class)) {
            $this->message = "cannot resolve the \"{$parameter}\" parameter in the \"{$class}\" class.";
        } elseif (is_callable($class)) {
            $refl = new ReflectionCallable($class);
            $this->line = $refl->getStartLine();
            $this->file = $refl->getFileName();
            $this->message = "cannot resolve the \"{$parameter}\" parameter in the {$refl->getCallableName()}";
        }
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
