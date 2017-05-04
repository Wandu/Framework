<?php
namespace Wandu\DI;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use Wandu\DI\Contracts\ClassDecoratorInterface;
use Wandu\DI\Contracts\MethodDecoratorInterface;
use Wandu\DI\Contracts\PropertyDecoratorInterface;
use Wandu\DI\Exception\CannotFindParameterException;

abstract class Descriptor
{
    /** @var mixed */
    public $cache;
    
    /** @var array */
    public $arguments = [];
    
    /** @var bool */
    public $factory = false;
    
    /** @var bool */
    public $annotated = false;
    
    /** @var bool */
    public $frozen = false;

    /**
     * @param array $arguments
     * @return \Wandu\DI\Descriptor
     */
    public function assign(array $arguments = []): Descriptor
    {
        $this->arguments = $arguments + $this->arguments;
        return $this;
    }

    /**
     * @return \Wandu\DI\Descriptor
     */
    public function freeze(): Descriptor
    {
        $this->frozen = true;
        return $this;
    }

    /**
     * @return \Wandu\DI\Descriptor
     */
    public function factory(): Descriptor
    {
        $this->factory = true;
        return $this;
    }

    /**
     * @return \Wandu\DI\Descriptor
     */
    public function annotated(): Descriptor
    {
        $this->annotated = true;
        return $this;
    }
    
    /**
     * @deprecated
     * 
     * @return \Wandu\DI\Descriptor
     */
    public function wire(): Descriptor
    {
        return $this->annotated();
    }

    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @return mixed
     */
    public function createInstance(ContainerInterface $container)
    {
        if ($this->factory) {
            $object = $this->create($container);
            $this->frozen = true;
            return $object;
        }
        if (!isset($this->cache)) {
            $this->cache = $this->create($container); // 
        }
        $this->frozen = true;
        return $this->cache;
    }

    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @return object
     */
    abstract protected function create(ContainerInterface $container);

    protected function getParameters(
        ContainerInterface $container,
        ReflectionFunctionAbstract $reflectionFunction
    ) {
        $arguments = $this->arguments;
        $parametersToReturn = static::getSeqArray($arguments);

        $reflectionParameters = array_slice($reflectionFunction->getParameters(), count($parametersToReturn));
        if (!count($reflectionParameters)) {
            return $parametersToReturn;
        }
        /* @var \ReflectionParameter $param */
        foreach ($reflectionParameters as $param) {
            /*
             * #1. search in arguments by parameter name
             * #1.1. search in arguments by class name
             * #2. if parameter has type hint
             * #2.1. search in container by class name
             * #3. if has default value, insert default value.
             * #4. exception
             */
            $paramName = $param->getName();
            try {
                if (array_key_exists($paramName, $arguments)) { // #1.
                    $parametersToReturn[] = $arguments[$paramName];
                    continue;
                }
                $paramClass = $param->getClass();
                if ($paramClass) { // #2.
                    $paramClassName = $paramClass->getName();
                    if ($container->has($paramClassName)) { // #2.1.
                        $parametersToReturn[] = $container->get($paramClassName);
                        continue;
                    }
                }
                if ($param->isDefaultValueAvailable()) { // #3.
                    $parametersToReturn[] = $param->getDefaultValue();
                    continue;
                }
                throw new CannotFindParameterException($paramName); // #4.
            } catch (ReflectionException $e) {
                throw new CannotFindParameterException($paramName);
            }
        }
        return $parametersToReturn;
    }

    /**
     * @param array $array
     * @return array
     */
    protected static function getSeqArray(array $array)
    {
        $arrayToReturn = [];
        foreach ($array as $key => $item) {
            if (is_int($key)) {
                $arrayToReturn[] = $item;
            }
        }
        return $arrayToReturn;
    }
  
    protected function getDecorators(Reader $reader, ReflectionClass $reflClass)
    {
        $classDecorators = [];
        $propertyDecorators = [];
        $methodDecorators = [];
        foreach ($reader->getClassAnnotations($reflClass) as $classAnnotation) {
            if ($classAnnotation instanceof ClassDecoratorInterface) {
                $classDecorators[] = [$classAnnotation, $reflClass];
            }
        }
        foreach ($reflClass->getProperties() as $reflProperty) {
            foreach ($reader->getPropertyAnnotations($reflProperty) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof PropertyDecoratorInterface) {
                    $propertyDecorators[] = [$propertyAnnotation, $reflProperty];
                }
            }
        }
        foreach ($reflClass->getMethods() as $reflMethod) {
            foreach ($reader->getMethodAnnotations($reflMethod) as $methodAnnotation) {
                if ($methodAnnotation instanceof MethodDecoratorInterface) {
                    $methodDecorators[] = [$methodAnnotation, $reflMethod];
                }
            }
        }
        return [
            $classDecorators,
            $propertyDecorators,
            $methodDecorators,
        ];
    }
}
