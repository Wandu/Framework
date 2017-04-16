<?php
namespace Wandu\DI\Exception;

use Interop\Container\Exception\NotFoundException;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use ReflectionClass;
use Wandu\Reflection\ReflectionCallable;

class CannotResolveException extends RuntimeException implements NotFoundExceptionInterface, NotFoundException
{
    /** @var string */
    protected $class;
    
    /** @var callable */
    protected $callee;

    /** @var string */
    protected $parameter;

    /**
     * @param string $classOrCallee
     * @param string $parameter
     */
    public function __construct($classOrCallee, $parameter)
    {
        if (is_string($classOrCallee) && class_exists($classOrCallee)) {
            $this->class = $classOrCallee;
            $refl = new ReflectionClass($classOrCallee);
            $this->file = $refl->getFileName();
            if ($propRefl = $refl->getConstructor()) {
                $this->line = $propRefl->getStartLine();
            } else {
                $this->line = $refl->getStartLine();
            }
            $this->message = "cannot resolve the \"{$parameter}\" parameter in the \"{$classOrCallee}\" class.";
        } elseif (is_callable($classOrCallee)) {
            $this->callee = $classOrCallee;
            $refl = new ReflectionCallable($classOrCallee);
            $this->line = $refl->getStartLine();
            $this->file = $refl->getFileName();
            $this->message = "cannot resolve the \"{$parameter}\" parameter in the {$refl->getCallableName()}.";
        }
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
     * @return callable
     */
    public function getCallee()
    {
        return $this->callee;
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}
