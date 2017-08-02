<?php
namespace Wandu\DI;

use InvalidArgumentException;
use ReflectionFunctionAbstract;
use ReflectionClass;
use ReflectionException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Wandu\DI\Contracts\ContainerFluent;
use Wandu\DI\Contracts\ResolverInterface;
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
    
    /** @var \Wandu\DI\Descriptor[] */
    protected $descriptors = [];

    /** @var array */
    protected $instances = [];

    /** @var array */
    protected $classes = [];
    
    /** @var array */
    protected $closures = [];
    
    /** @var array */
    protected $aliases = [];

    /** @var \Wandu\DI\ServiceProviderInterface[] */
    protected $providers = [];

    /** @var bool */
    protected $isBooted = false;

    public function __construct(array $options = [])
    {
        $this->instances = [
            Container::class => $this,
            ContainerInterface::class => $this,
            PsrContainerInterface::class => $this,
            'container' => $this,
        ];
    }

    public function __clone()
    {
        $this->instances[Container::class] = $this;
        $this->instances[ContainerInterface::class] = $this;
        $this->instances[PsrContainerInterface::class] = $this;
        $this->instances['container'] = $this;
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
        if (isset($this->descriptors[$name])) {
            $this->descriptors[$name]->freeze();
        }
        return $this->resolve($name);
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
        try {
            $this->resolve($name);
            return true;
        } catch (NullReferenceException $e) {
        } catch (CannotResolveException $e) {
        }
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function destroy(...$names)
    {
        foreach ($names as $name) {
            if (array_key_exists($name, $this->descriptors)) {
                if ($this->descriptors[$name]->frozen) {
                    throw new CannotChangeException($name);
                }
            }
            unset(
                $this->descriptors[$name],
                $this->instances[$name],
                $this->classes[$name],
                $this->closures[$name]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function instance(string $name, $value): ContainerFluent
    {
        $this->destroy($name);
        $this->instances[$name] = $value;
        return $this->descriptors[$name] = new Descriptor();
    }

    /**
     * @deprecated
     */
    public function closure(string $name, callable $handler): ContainerFluent
    {
        return $this->bind($name, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $name, $className = null): ContainerFluent
    {
        if (!isset($className)) {
            $this->destroy($name);
            $this->classes[$name] = $name;
            return $this->descriptors[$name] = new Descriptor();
        }
        if (is_string($className) && class_exists($className)) {
            $this->destroy($name);
            $this->classes[$name] = $className;
            $this->alias($className, $name);
            return $this->descriptors[$name] = new Descriptor();
        } elseif (is_callable($className)) {
            $this->destroy($name);
            $this->closures[$name] = $className;
            return $this->descriptors[$name] = new Descriptor();
        }
        throw new InvalidArgumentException(
            sprintf('Argument 2 must be class name or Closure, "%s" given', is_object($className) ? get_class($className) : gettype($className))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function alias(string $alias, string $target)
    {
        $this->aliases[$alias] = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function descriptor(string $name): ContainerFluent
    {
        while (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }
        if (!array_key_exists($name, $this->descriptors)) {
            throw new NullReferenceException($name);
        }
        return $this->descriptors[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $arguments = []): ContainerInterface
    {
        $new = clone $this;
        foreach ($arguments as $name => $argument) {
            $new->instance($name, $argument);
        }
        return $new;
    }
    
    /**
     * {@inheritdoc}
     */
    public function create(string $className, array $arguments = [])
    {
        if (!class_exists($className)) {
            throw new NullReferenceException($className);
        }
        try {
            if ($constructor = (new ReflectionClass($className))->getConstructor()) {
                $arguments = $this->getParameters($constructor, $arguments);
                return new $className(...$arguments);
            }
        } catch (CannotFindParameterException $e) {
            throw new CannotResolveException($className, $e->getParameter());
        }
        return new $className;
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

    protected function resolve($name)
    {
        while (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }
        if (array_key_exists($name, $this->instances)) {
            return $this->instances[$name];
        }
        if (!array_key_exists($name, $this->descriptors)) {
            if (!class_exists($name)) {
                throw new NullReferenceException($name);
            }
            $this->bind($name);
        }
        $descriptor = $this->descriptors[$name];
        if (array_key_exists($name, $this->classes)) {
            $instance = $this->create($this->classes[$name], $this->getArgumentFromDescriptor($descriptor));
        } elseif (array_key_exists($name, $this->closures)) {
            $instance = $this->call($this->closures[$name], $this->getArgumentFromDescriptor($descriptor));
        }
        foreach ($descriptor->afterHandlers as $handler) {
            call_user_func($handler, $instance);
        }
        if (!$descriptor->factory) {
            $this->instances[$name] = $instance;
        }
        return $instance;
    }

    /**
     * @param \Wandu\DI\Descriptor $descriptor
     * @return array
     */
    protected function getArgumentFromDescriptor(Descriptor $descriptor)
    {
        $arguments = [];
        foreach ($descriptor->assigns as $key => $value) {
            try {
                $arguments[$key] = $this->get($value);
            } catch (NullReferenceException $e) {}
        }
        return $descriptor->values + $arguments;
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
                    if (array_key_exists($paramClassName, $arguments)) {
                        $parametersToReturn[] = $arguments[$paramClassName];
                        continue;
                    } else { // #2.1.
                        try {
                            $parametersToReturn[] = $this->get($paramClassName);
                            continue;
                        } catch (NullReferenceException $e) {}
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
}
