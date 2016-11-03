<?php
namespace Wandu\DI;

use Closure;
use Doctrine\Common\Annotations\Reader;
use Exception;
use Interop\Container\ContainerInterface as InteropContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionObject;
use ReflectionProperty;
use Throwable;
use Wandu\DI\Annotations\AutoWired;
use Wandu\DI\Containee\BindContainee;
use Wandu\DI\Containee\ClosureContainee;
use Wandu\DI\Containee\InstanceContainee;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotFindParameterException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\Reflection\ReflectionCallable;

class Container implements ContainerInterface
{
    /** @var \Wandu\DI\Containee\ContaineeAbstract[] */
    protected $containees = [];
    
    /** @var \Wandu\DI\ServiceProviderInterface[] */
    protected $providers = [];

    /** @var array */
    protected $extenders = [];
    
    /** @var array */
    protected $indexOfAliases = [];
    
    /** @var bool */
    protected $isBooted = false;

    public function __construct()
    {
        $this->instance(Container::class, $this)->freeze();
        $this->instance(ContainerInterface::class, $this)->freeze();
        $this->instance(InteropContainerInterface::class, $this)->freeze();
        $this->instance('container', $this)->freeze();
    }

    public function __clone()
    {
        // direct remove instance because of frozen
        unset(
            $this->containees[Container::class],
            $this->containees[ContainerInterface::class],
            $this->containees[InteropContainerInterface::class],
            $this->containees['container']
        );
        $this->instance(Container::class, $this)->freeze();
        $this->instance(ContainerInterface::class, $this)->freeze();
        $this->instance(InteropContainerInterface::class, $this)->freeze();
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
        $this->set($name, $value);
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
    public function containee($name)
    {
        if (!array_key_exists($name, $this->containees)) {
            if (class_exists($name)) {
                $this->bind($name);
            } else {
                throw new NullReferenceException($name);
            }
        }
        return $this->containees[$name];
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $instance = $this->containee($name)->get($this);
        if ($this->containees[$name]->isWireEnabled()) {
            $this->applyWire($instance);
        }
        foreach ($this->getExtenders($name) as $extender) {
            $instance = $extender->__invoke($instance);
        }
        return $instance;
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
        if (isset($this->indexOfAliases[$name])) {
            foreach ($this->indexOfAliases[$name] as $aliasName) {
                $extenders = array_merge($extenders, $this->getExtenders($aliasName));
            }
        }
        return $extenders;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        if (!($value instanceof ContaineeInterface)) {
            $value = new InstanceContainee($value);
        }
        return $this->addContainee($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function instance($name, $value)
    {
        return $this->addContainee($name, new InstanceContainee($value));
    }

    /**
     * {@inheritdoc}
     */
    public function closure($name, callable $handler)
    {
        return $this->addContainee($name, new ClosureContainee($handler));
    }

    /**
     * {@inheritdoc}
     */
    public function alias($name, $origin)
    {
        if (!array_key_exists($origin, $this->indexOfAliases)) {
            $this->indexOfAliases[$origin] = [];
        }
        $this->indexOfAliases[$origin][] = $name;
        return $this->closure($name, function (ContainerInterface $container) use ($origin) {
            return $container->get($origin); // proxy
        })->factory(true);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($name, $class = null)
    {
        if (isset($class)) {
            $this->alias($class, $name);
            return $this->addContainee($name, new BindContainee($class));
        }
        return $this->addContainee($name, new BindContainee($name));
    }
    
    /**
     * @param string $name
     * @param \Wandu\DI\ContaineeInterface $containee
     * @return \Wandu\DI\ContaineeInterface
     */
    public function addContainee($name, ContaineeInterface $containee)
    {
        $this->destroy($name);
        return $this->containees[$name] = $containee;
    }

    /**
     * {@inheritdoc}
     */
    public function extend($name, Closure $handler)
    {
        if (!array_key_exists($name, $this->extenders)) {
            $this->extenders[$name] = [];
        }
        $this->extenders[$name][] = $handler;
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
     * {@inheritdoc}
     */
    public function freeze($name)
    {
        $this->containees[$name]->freeze();
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $arguments = [])
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
    public function create($class, array $arguments = [])
    {
        $reflectionClass = new ReflectionClass($class);
        $reflectionMethod = $reflectionClass->getConstructor();
        if (!$reflectionMethod) {
            return $reflectionClass->newInstance();
        }
        try {
            $parameters = $this->getParameters($reflectionMethod, $arguments);
        } catch (CannotFindParameterException $e) {
            throw new CannotResolveException($class, $e->getParameter());
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
            throw new CannotResolveException(null, $e->getParameter());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function inject($object, array $properties = [])
    {
        $reflectionObject = new ReflectionObject($object);
        foreach ($properties as $property => $value) {
            $this->injectProperty($reflectionObject->getProperty($property), $object, $value);
        }
    }

    /**
     * @param \ReflectionProperty $property
     * @param object $object
     * @param mixed $target
     */
    private function injectProperty(ReflectionProperty $property, $object, $target)
    {
        $property->setAccessible(true);
        $property->setValue($object, $target);
    }

    /**
     * @param \ReflectionFunctionAbstract $reflectionFunction
     * @param array $arguments
     * @return array
     */
    protected function getParameters(ReflectionFunctionAbstract $reflectionFunction, array $arguments = [])
    {
        $parametersToReturn = static::getSeqArray($arguments);

        $reflectionParameters = array_slice($reflectionFunction->getParameters(), count($parametersToReturn));
        if (!count($reflectionParameters)) {
            return $parametersToReturn; 
        }
        
        try {
            /* @var \ReflectionParameter $param */
            foreach ($reflectionParameters as $param) {
                /*
                 * #1. search in arguments by parameter name
                 * #2. if parameter has type hint
                 * #2.1. search in container by class name
                 * #3. if has default value, insert default value.
                 * #4. exception
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
                if ($param->isDefaultValueAvailable()) { // #3.
                    $parametersToReturn[] = $param->getDefaultValue();
                    continue;
                }
                throw new CannotFindParameterException($paramName); // #4.
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
    
    protected function applyWire($instance)
    {
        static $callStack = [];
        if (in_array($instance, $callStack)) {
            return; // return when a circular call is detected.
        }
        array_push($callStack, $instance);
        try {
            /* @var \Doctrine\Common\Annotations\Reader $reader */
            $reader = $this->get(Reader::class);
            class_exists(AutoWired::class); // pre-load for Annotation Reader
            $reflObject = new ReflectionObject($instance);
            foreach ($reflObject->getProperties() as $reflProperty) {
                /* @var \Wandu\DI\Annotations\AutoWired $autoWired */
                if ($autoWired = $reader->getPropertyAnnotation($reflProperty, AutoWired::class)) {
                    $this->inject($instance, [
                        $reflProperty->getName() => $this->get($autoWired->name),
                    ]);
                }
            }
        } catch (Exception $e) {
            array_pop($callStack);
            throw $e;
        } catch (Throwable $e) {
            array_pop($callStack);
            throw $e;
        }
        array_pop($callStack);
    }
}
