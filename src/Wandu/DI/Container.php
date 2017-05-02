<?php
namespace Wandu\DI;

use Closure;
use Doctrine\Common\Annotations\Reader;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionObject;
use Wandu\DI\Annotations\AutoWired;
use Wandu\DI\Containee\BindContainee;
use Wandu\DI\Containee\ClosureContainee;
use Wandu\DI\Containee\InstanceContainee;
use Wandu\DI\Contracts\ClassDecoratorInterface;
use Wandu\DI\Contracts\MethodDecoratorInterface;
use Wandu\DI\Contracts\PropertyDecoratorInterface;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotFindParameterException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Exception\RequirePackageException;
use Wandu\Reflection\ReflectionCallable;

class Container implements ContainerInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    public static $instance;
    
    /** @var \Wandu\DI\Containee\ContaineeAbstract[] */
    protected $containees = [];

    /** @var array */
    protected $instances = [];
    
    /** @var \Wandu\DI\ServiceProviderInterface[] */
    protected $providers = [];

    /** @var array */
    protected $extenders = [];
    
    /** @var array */
    protected $aliases = [];
    
    /** @var bool */
    protected $isBooted = false;

    public function __construct()
    {
        $this->instance(Container::class, $this)->freeze();
        $this->instance(ContainerInterface::class, $this)->freeze();
        $this->instance(PsrContainerInterface::class, $this)->freeze();
        $this->instance('container', $this)->freeze();
    }

    /**
     * @return \Wandu\DI\ContainerInterface
     */
    public function setAsGlobal()
    {
        $instance = static::$instance;
        static::$instance = $this;
        return $instance;
    }

    public function __clone()
    {
        // direct remove instance because of frozen
        unset(
            $this->containees[Container::class],
            $this->containees[ContainerInterface::class],
            $this->containees[PsrContainerInterface::class],
            $this->containees['container']
        );
        $this->instance(Container::class, $this)->freeze();
        $this->instance(ContainerInterface::class, $this)->freeze();
        $this->instance(PsrContainerInterface::class, $this)->freeze();
        $this->instance('container', $this)->freeze();
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, array $arguments)
    {
        return $this->call($this->get($name), $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name)
    {
        return $this->has($name) && $this->get($name) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($name, $value)
    {
        $this->instance($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($name)
    {
        $this->destroy($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        try {
//            if ($this->containees[$name]->isAnnotatedEnabled()) {
//                $this->annotateBeforeCreate($name);
//            }
            $instance = $this->containee($name)->get($this);
        } catch (NullReferenceException $e) {
            if (!class_exists($name)) {
                throw $e;
            }
            $instance = $this->create($name);
            $this->instance($name, $instance);
        }
        if ($this->containees[$name]->isAnnotatedEnabled()) {
            $this->annotateAfterCreate($name, $instance);
        }
        foreach ($this->getExtenders($name) as $extender) {
            $instance = $extender->__invoke($instance);
        }
        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function assert(string $name, string $package)
    {
        try {
            return $this->get($name);
        } catch (ContainerExceptionInterface $e) {
            throw new RequirePackageException($name, $package);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return array_key_exists($name, $this->containees) || class_exists($name);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(...$names)
    {
        foreach ($names as $name) {
            if (array_key_exists($name, $this->containees)) {
                if ($this->containees[$name]->isFrozen()) {
                    throw new CannotChangeException($name);
                }
            }
            unset($this->containees[$name]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function instance(string $name, $value): ContaineeInterface
    {
        return $this->addContainee($name, new InstanceContainee($value));
    }

    /**
     * {@inheritdoc}
     */
    public function closure(string $name, callable $handler): ContaineeInterface
    {
        return $this->addContainee($name, new ClosureContainee($handler));
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $name, string $className = null): ContaineeInterface
    {
        if (isset($className)) {
            $this->alias($className, $name);
            return $this->addContainee($name, new BindContainee($className));
        }
        return $this->addContainee($name, new BindContainee($name));
    }

    /**
     * {@inheritdoc}
     */
    public function alias(string $alias, string $target)
    {
        if (!array_key_exists($target, $this->aliases)) {
            $this->aliases[$target] = [];
        }
        $this->aliases[$target][] = $alias;
        $this->closure($alias, function (ContainerInterface $container) use ($target) {
            return $container->get($target); // proxy
        })->factory(true);
    }

    /**
     * {@inheritdoc}
     */
    public function containee(string $name): ContaineeInterface
    {
        if (!array_key_exists($name, $this->containees)) {
            throw new NullReferenceException($name);
        }
        return $this->containees[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $arguments = []): ContainerInterface
    {
        $new = clone $this;
        foreach ($arguments as $name => $argument) {
            if ($argument instanceof ContaineeInterface) {
                $new->addContainee($name, $argument);
            } else {
                $new->instance($name, $argument);
            }
        }
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function extend(string $name, Closure $handler)
    {
        if (!array_key_exists($name, $this->extenders)) {
            $this->extenders[$name] = [];
        }
        $this->extenders[$name][] = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $className, array $arguments = [])
    {
        $reflectionClass = new ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getConstructor();
        if (!$reflectionMethod) {
            return $reflectionClass->newInstance();
        }
        try {
            $parameters = $this->getParameters($reflectionMethod, $arguments);
        } catch (CannotFindParameterException $e) {
            throw new CannotResolveException($className, $e->getParameter());
        }
        return $reflectionClass->newInstanceArgs($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function call(callable $callee, array $arguments = [])
    {
        try {
            return call_user_func_array(
                $callee,
                $this->getParameters(new ReflectionCallable($callee), $arguments)
            );
        } catch (CannotFindParameterException $e) {
            throw new CannotResolveException($callee, $e->getParameter());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register(ServiceProviderInterface $provider)
    {
        $provider->register($this);
        $this->providers[] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (!$this->isBooted) {
            foreach ($this->providers as $provider) {
                $provider->boot($this);
            }
            $this->isBooted = true;
        }
        return $this;
    }

    /**
     * @param string $name
     * @return \Closure[]
     */
    protected function getExtenders($name)
    {
        $extenders = [];
        if (isset($this->extenders[$name])) {
            $extenders = array_merge($extenders, $this->extenders[$name]);
        }

        // extend propagation
        if (isset($this->aliases[$name])) {
            foreach ($this->aliases[$name] as $aliasName) {
                $extenders = array_merge($extenders, $this->getExtenders($aliasName));
            }
        }
        return $extenders;
    }

    /**
     * @param string $name
     * @param \Wandu\DI\ContaineeInterface $containee
     * @return \Wandu\DI\ContaineeInterface
     */
    protected function addContainee($name, ContaineeInterface $containee): ContaineeInterface
    {
        $this->destroy($name);
        return $this->containees[$name] = $containee;
    }
    
    /**
     * @param \ReflectionFunctionAbstract $reflectionFunction
     * @param array $arguments
     * @return array
     * @throws \Wandu\DI\Exception\CannotFindParameterException
     */
    protected function getParameters(ReflectionFunctionAbstract $reflectionFunction, array $arguments = [])
    {
        $parametersToReturn = static::getSeqArray($arguments);

        $reflectionParameters = array_slice($reflectionFunction->getParameters(), count($parametersToReturn));
        if (!count($reflectionParameters)) {
            return $parametersToReturn; 
        }
        
        $autoWires = [];
        if ($reflectionFunction instanceof ReflectionMethod) {
            $declaredClassName = $reflectionFunction->getDeclaringClass()->getName();
            if (isset($this->containees[$declaredClassName]) && $this->containees[$declaredClassName]->isWireEnabled()) {
                $autoWires = $this->getAutoWiresFromMethod($reflectionFunction);
            }
        } elseif (
            $reflectionFunction instanceof ReflectionCallable &&
            $reflectionFunction->getRawReflection() instanceof ReflectionMethod
        ) {
            $declaredClassName = $reflectionFunction->getRawReflection()->getDeclaringClass()->getName();
            if (isset($this->containees[$declaredClassName]) && $this->containees[$declaredClassName]->isWireEnabled()) {
                $autoWires = $this->getAutoWiresFromMethod($reflectionFunction->getRawReflection());
            }
        }
        
        try {
            /* @var \ReflectionParameter $param */
            foreach ($reflectionParameters as $param) {
                /*
                 * #1. search in arguments by parameter name
                 * #2. if parameter has type hint
                 * #2.1. search in container by class name
                 * #3. if autowired enabled
                 * #3.1. search in container by autowired name
                 * #4. if has default value, insert default value.
                 * #5. exception
                 */
                $paramName = $param->getName();
                if (array_key_exists($paramName, $arguments)) { // #1.
                    $parametersToReturn[] = $arguments[$paramName];
                    continue;
                }
                $paramClass = $param->getClass();
                if ($paramClass) { // #2.
                    $paramClassName = $paramClass->getName();
                    if ($this->has($paramClassName)) { // #2.1.
                        $parametersToReturn[] = $this->get($paramClassName);
                        continue;
                    }
                }
                if (array_key_exists($paramName, $autoWires) && $this->has($autoWires[$paramName])) {
                    $parametersToReturn[] = $this->get($autoWires[$paramName]);
                    continue;
                }
                if ($param->isDefaultValueAvailable()) { // #4.
                    $parametersToReturn[] = $param->getDefaultValue();
                    continue;
                }
                throw new CannotFindParameterException($paramName); // #5.
            }
        } catch (ReflectionException $e) {
            throw new CannotFindParameterException($paramName);
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

    protected function annotateBeforeCreate($className)
    {
        if (!class_exists($className)) return;
        $reader = $this->get(Reader::class);
        $reflClass = new ReflectionClass($className);

        foreach ($reader->getClassAnnotations($reflClass) as $classAnnotation) {
            if ($classAnnotation instanceof ClassDecoratorInterface) {
                $classAnnotation->beforeCreateClass($reflClass, $this);
            }
        }
        foreach ($reflClass->getProperties() as $reflProperty) {
            foreach ($reader->getPropertyAnnotations($reflProperty) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof PropertyDecoratorInterface) {
                    $propertyAnnotation->decoratePropertyBeforeCreate($reflProperty, $this);
                }
            }
        }
        foreach ($reflClass->getMethods() as $reflMethod) {
            foreach ($reader->getMethodAnnotations($reflMethod) as $methodAnnotation) {
                if ($methodAnnotation instanceof MethodDecoratorInterface) {
                    $methodAnnotation->decorateMethodBeforeCreate($reflMethod, $this);
                }
            }
        }
    }
    
    protected function annotateAfterCreate($className, $instance)
    {
        if (!is_object($instance)) return;
        $reader = $this->get(Reader::class);
        $reflObject = new ReflectionObject($instance);
        
        foreach ($reader->getClassAnnotations($reflObject) as $classAnnotation) {
            if ($classAnnotation instanceof ClassDecoratorInterface) {
                $classAnnotation->onCreateClass($instance, $reflObject, $this);
            }
        }
        foreach ($reflObject->getProperties() as $reflProperty) {
            foreach ($reader->getPropertyAnnotations($reflProperty) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof PropertyDecoratorInterface) {
                    $propertyAnnotation->decorateProperty($instance, $reflProperty, $this);
                }
            }
        }
        foreach ($reflObject->getMethods() as $reflMethod) {
            foreach ($reader->getMethodAnnotations($reflMethod) as $methodAnnotation) {
                if ($methodAnnotation instanceof MethodDecoratorInterface) {
                    $methodAnnotation->afterCreateMethod($instance, $reflMethod, $this);
                }
            }
        }
    }

    /**
     * @param \ReflectionMethod $reflMethod
     * @return array
     */
    protected function getAutoWiresFromMethod(ReflectionMethod $reflMethod)
    {
        $reader = $this->get(Reader::class);
        class_exists(AutoWired::class); // pre-load for Annotation Reader
        $autoWires = [];
        foreach ($reader->getMethodAnnotations($reflMethod) as $annotation) {
            if ($annotation instanceof AutoWired) {
                $autoWires[$annotation->to] = $annotation->name;
            }
        }
        return $autoWires;
    }
}
