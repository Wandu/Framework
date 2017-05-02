<?php
namespace Wandu\DI\Containee;

use Doctrine\Common\Annotations\Reader;
use Wandu\DI\ContaineeInterface;
use Wandu\DI\ContainerInterface;
use ReflectionMethod;
use ReflectionObject;
use ReflectionFunctionAbstract;
use Wandu\DI\Contracts\ClassDecoratorInterface;
use Wandu\DI\Contracts\MethodDecoratorInterface;
use Wandu\DI\Contracts\PropertyDecoratorInterface;
use Wandu\DI\Exception\CannotFindParameterException;
use ReflectionException;

abstract class ContaineeAbstract implements ContaineeInterface
{
    /** @var mixed */
    protected $caching;
    
    /** @var array */
    protected $attributes = [];
    
    /** @var bool */
    protected $factoryEnabled = false;
    
    /** @var bool */
    protected $annotatedEnabled = false;
    
    /** @var bool */
    protected $wireEnabled = false;
    
    /** @var bool */
    protected $frozen = false;
    
    /**
     * {@inheritdoc}
     */
    public function assign(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function freeze()
    {
        $this->frozen = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFrozen()
    {
        return $this->frozen;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($enabled = true)
    {
        $this->factoryEnabled = $enabled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFactoryEnabled()
    {
        return $this->factoryEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function annotated($enabled = true)
    {
        $this->annotatedEnabled = $enabled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAnnotatedEnabled(): bool
    {
        return $this->annotatedEnabled;
    }
    
    /**
     * {@inheritdoc}
     */
    public function wire($enabled = true)
    {
        $this->annotatedEnabled = true; // if you use autowired, use annotation!
        $this->wireEnabled = $enabled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isWireEnabled()
    {
        return $this->wireEnabled;
    }

    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @return mixed
     */
    public function get(ContainerInterface $container)
    {
        if ($this->factoryEnabled) {
            $object = $this->create($container);
            $this->frozen = true;
            return $object;
        }
        if (!isset($this->caching)) {
            $this->caching = $this->create($container);
            $this->frozen = true;
        }
        return $this->caching;
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
        $arguments = $this->attributes;
        $parametersToReturn = static::getSeqArray($arguments);

        $reflectionParameters = array_slice($reflectionFunction->getParameters(), count($parametersToReturn));
        if (!count($reflectionParameters)) {
            return $parametersToReturn;
        }
        $autoWires = [];
//        if ($reflectionFunction instanceof ReflectionMethod) {
//            $declaredClassName = $reflectionFunction->getDeclaringClass()->getName();
//            if ($container->containee($declaredClassName)->isWireEnabled()) {
//                $autoWires = $this->getAutoWiresFromMethod($reflectionFunction);
//            }
//        } elseif (
//            $reflectionFunction instanceof ReflectionCallable &&
//            $reflectionFunction->getRawReflection() instanceof ReflectionMethod
//        ) {
//            $declaredClassName = $reflectionFunction->getRawReflection()->getDeclaringClass()->getName();
//            if (isset($this->containees[$declaredClassName]) && $this->containees[$declaredClassName]->isWireEnabled()) {
//                $autoWires = $this->getAutoWiresFromMethod($reflectionFunction->getRawReflection());
//            }
//        }
//        if ($this->annotatedEnabled) {
//            $reader = $container->get(Reader::class);
//            $reflClass = new ReflectionClass($this->className);
//            foreach ($reader->getClassAnnotations($reflClass) as $classAnnotation) {
//                if ($classAnnotation instanceof ClassDecoratorInterface) {
//                    $classAnnotation->beforeCreateClass($reflClass, $container);
//                }
//            }
//            foreach ($reflClass->getProperties() as $reflProperty) {
//                foreach ($reader->getPropertyAnnotations($reflProperty) as $propertyAnnotation) {
//                    if ($propertyAnnotation instanceof PropertyDecoratorInterface) {
//                        $propertyAnnotation->beforeCreateProperty($reflProperty, $container);
//                    }
//                }
//            }
//            foreach ($reflClass->getMethods() as $reflMethod) {
//                foreach ($reader->getMethodAnnotations($reflMethod) as $methodAnnotation) {
//                    if ($methodAnnotation instanceof MethodDecoratorInterface) {
//                        $methodAnnotation->beforeCreateMethod($reflMethod, $container);
//                    }
//                }
//            }
//        }        
        /* @var \ReflectionParameter $param */
        foreach ($reflectionParameters as $param) {
            /*
             * #1. search in arguments by parameter name
             * #1.1. search in arguments by class name
             * #2. if parameter has type hint
             * #2.1. search in container by class name
             * #3. if autowired enabled
             * #3.1. search in container by autowired name
             * #4. if has default value, insert default value.
             * #5. exception
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
                if (array_key_exists($paramName, $autoWires) && $container->has($autoWires[$paramName])) {
                    $parametersToReturn[] = $this->get($autoWires[$paramName]);
                    continue;
                }
                if ($param->isDefaultValueAvailable()) { // #4.
                    $parametersToReturn[] = $param->getDefaultValue();
                    continue;
                }
                throw new CannotFindParameterException($paramName); // #5.
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

    protected function annotateAfterCreate(ContainerInterface $container, $instance)
    {
        if (!is_object($instance)) return;
        $reader = $container->get(Reader::class);
        $reflObject = new ReflectionObject($instance);

        foreach ($reader->getClassAnnotations($reflObject) as $classAnnotation) {
            if ($classAnnotation instanceof ClassDecoratorInterface) {
                $classAnnotation->afterCreateClass($instance, $reflObject, $container);
            }
        }
        foreach ($reflObject->getProperties() as $reflProperty) {
            foreach ($reader->getPropertyAnnotations($reflProperty) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof PropertyDecoratorInterface) {
                    $propertyAnnotation->decorateProperty($instance, $reflProperty, $container);
                }
            }
        }
        foreach ($reflObject->getMethods() as $reflMethod) {
            foreach ($reader->getMethodAnnotations($reflMethod) as $methodAnnotation) {
                if ($methodAnnotation instanceof MethodDecoratorInterface) {
                    $methodAnnotation->afterCreateMethod($instance, $reflMethod, $container);
                }
            }
        }
    }
//
//    /**
//     * @param \ReflectionMethod $reflMethod
//     * @return array
//     */
//    protected function getAutoWiresFromMethod(ReflectionMethod $reflMethod)
//    {
//        $reader = $this->get(Reader::class);
//        class_exists(AutoWired::class); // pre-load for Annotation Reader
//        $autoWires = [];
//        foreach ($reader->getMethodAnnotations($reflMethod) as $annotation) {
//            if ($annotation instanceof AutoWired) {
//                $autoWires[$annotation->to] = $annotation->name;
//            }
//        }
//        return $autoWires;
//    }
}
