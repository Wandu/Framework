<?php
namespace Wandu\DI;

use Closure;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionObject;
use ReflectionProperty;
use RuntimeException;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotInjectException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\Reflection\ReflectionCallable;

class Container implements ContainerInterface
{
    /** @var array */
    protected $keys = [];

    /** @var array */
    protected $closures = [];

    /** @var array */
    protected $instances = [];

    /** @var array */
    protected $aliases = [];

    /** @var array */
    protected $bind = [];

    /** @var array ref. Pimple */
    protected $frozen = [];

    /** @var \Wandu\DI\ServiceProviderInterface[] */
    protected $providers = [];

    public function __construct()
    {
        $this->instance(ContainerInterface::class, $this)
            ->freeze(ContainerInterface::class);
        $this->alias('container', ContainerInterface::class)
            ->freeze('container');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($name)
    {
        return $this->has($name);
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
    public function has($name)
    {
        return isset($this->keys[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($name)
    {
        if (isset($this->frozen[$name])) {
            throw new CannotChangeException($name);
        }
        unset(
            $this->keys[$name],
            $this->closures[$name],
            $this->instances[$name],
            $this->aliases[$name],
            $this->bind[$name]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!isset($this->keys[$name])) {
            throw new NullReferenceException($name);
        }
        $this->freeze($name);
        if ('alias' === $key = $this->keys[$name]) {
            return $this->get($this->aliases[$name]);
        }
        if (!isset($this->instances[$name])) {
            switch ($key) {
                case 'closure':
                    $this->instances[$name] = call_user_func($this->closures[$name], $this);
                    break;
                case 'bind':
                    $this->instances[$name] = $this->create($this->bind[$name]);
                    break;
                case 'wire':
                    $this->instances[$name] = $this->create($this->bind[$name]);
                    $this->inject($this->instances[$name]);
                    break;
            }
        }
        return $this->instances[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function instance($name, $value)
    {
        $this->destroy($name);
        $this->keys[$name] = 'instance';
        $this->instances[$name] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function closure($name, Closure $handler)
    {
        $this->destroy($name);
        $this->keys[$name] = 'closure';
        $this->closures[$name] = $handler;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function bind($name, $class = null)
    {
        if (!isset($class)) {
            $class = $name;
        }
        $this->destroy($name);
        $this->keys[$name] = 'bind';
        $this->bind[$name] = $class;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function alias($name, $origin)
    {
        $this->destroy($name);
        $this->keys[$name] = 'alias';
        $this->aliases[$name] = $origin;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function wire($name, $class = null)
    {
        $this->bind($name, $class);
        $this->keys[$name] = 'wire';
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function extend($name, Closure $handler)
    {
        if (!isset($this->keys[$name])) {
            throw new NullReferenceException($name);
        }
        if (isset($this->aliases[$name])) {
            $this->extend($this->aliases[$name], $handler);
            return $this;
        }
        if (isset($this->instances[$name])) {
            $this->instances[$name] = call_user_func($handler, $this->instances[$name]);
        }
        if (isset($this->closures[$name])) {
            $closure = $this->closures[$name];
            $this->closures[$name] = function () use ($closure, $handler) {
                return call_user_func($handler, call_user_func($closure, $this));
            };
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function register(ServiceProviderInterface $provider)
    {
        $provider->register($this);
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function freeze($name)
    {
        $this->frozen[$name] = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create($class, array $arguments = [])
    {
        $reflectionClass = new ReflectionClass($class);
        $reflectionMethod = $reflectionClass->getConstructor();
        if ($reflectionMethod) {
            try {
                $parameters = $this->getParameters($reflectionMethod, $arguments);
            } catch (RuntimeException $e) {
                throw new CannotResolveException($class);
            }
        } else {
            $parameters = [];
        }
        return $reflectionClass->newInstanceArgs($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function call(callable $callee, array $arguments = [])
    {
        return call_user_func_array(
            $callee,
            $this->getParameters(new ReflectionCallable($callee), $arguments)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function inject($object, array $properties = [])
    {
        $reflectionObject = new ReflectionObject($object);
        foreach ($reflectionObject->getProperties() as $property) {
            // if property doesn't have doc type hint,
            // 1.1. search in properties by property name

            // if property has doc type hint,
            // 2.1. search in properties by property name ( == 1.1)
            // 2.2. search in properties by class name (in doc)
            // 2.3. if has doc @Autowired then search in container by class name (in doc)
            //      else exception!

            // 1.1, 2.1
            if (array_key_exists($propertyName = $property->getName(), $properties)) {
                $this->injectProperty($property, $object, $properties[$propertyName]);
                continue;
            }
            $docComment = $property->getDocComment();
            $propertyClassName = $this->getClassNameFromDocComment($docComment);
            if ($propertyClassName) {
                // 2.2
                if (array_key_exists($propertyClassName, $properties)) {
                    $this->injectProperty($property, $object, $properties[$propertyClassName]);
                    continue;
                }
                // 2.3
                if ($this->hasAutowiredFromDocComment($docComment)) {
                    if ($this->has($propertyClassName)) {
                        $this->injectProperty($property, $object, $this->get($propertyClassName));
                        continue;
                    } else {
                        throw new CannotInjectException($propertyClassName, $property->getName());
                    }
                }
            }
        }
    }

    /**
     * @param \ReflectionProperty $property
     * @param object $object
     * @param mixed $target
     */
    protected function injectProperty(ReflectionProperty $property, $object, $target)
    {
        $property->setAccessible(true);
        $property->setValue($object, $target);
    }

    /**
     * @param string $comment
     * @return string
     */
    protected function getClassNameFromDocComment($comment)
    {
        $varPosition = strpos($comment, '@var');
        if ($varPosition === false) {
            return null;
        }
        preg_match('/^([a-zA-Z0-9\\\\]+)/', ltrim(substr($comment, $varPosition + 4)), $matches);
        $className =  $matches[0];
        if ($className[0] === '\\') {
            $className = substr($className, 1);
        }
        return $className;
    }

    /**
     * @param string $comment
     * @return bool
     */
    protected function hasAutowiredFromDocComment($comment)
    {
        return strpos($comment, '@autowired') !== false ||
            strpos($comment, '@Autowired') !== false ||
            strpos($comment, '@AUTOWIRED') !== false;
    }

    /**
     * @param \ReflectionFunctionAbstract $reflectionFunction
     * @param array $arguments
     * @return array
     */
    protected function getParameters(ReflectionFunctionAbstract $reflectionFunction, array $arguments)
    {
        $parametersToReturn = [];
        $parameters = $this->getOnlySeqArray($arguments);
        foreach ($reflectionFunction->getParameters() as $param) {
            // if parameter doesn't have type hint,
            // 1.1. search in arguments by param name
            // 1.2. insert remain arguments
            // 1.3. if has default value, insert default value.
            // 1.4. exception

            // if parameter has type hint,
            // 2.1. search in arguments by param name ( == 1.1)
            // 2.2. search in arguments by class name
            // 2.3. search in container by class name
            // 2.4. if has default value, insert default vlue. ( == 1.3)
            // 2.5. exception ( == 1.4)a

            // 1.1, 2.1
            if (isset($arguments[$paramName = $param->getName()])) {
                $parametersToReturn[] = $arguments[$paramName];
                continue;
            }
            if ($paramClassReflection = $param->getClass()) { // 2.*
                // 2.2
                $paramClassName = $paramClassReflection->getName();
                if (isset($arguments[$paramClassName])) {
                    $parametersToReturn[] = $arguments[$paramClassName];
                    continue;
                }
                // 2.3
                if ($this->has($paramClassName)) {
                    $parametersToReturn[] = $this->get($paramClassName);
                    continue;
                }
            } else { // 1.*
                // 1.2
                if (count($parameters)) {
                    $parametersToReturn[] = array_shift($parameters);
                    continue;
                }
            }
            // 1.3, 2.4
            if ($param->isDefaultValueAvailable()) {
                $parametersToReturn[] = $param->getDefaultValue();
                continue;
            }
            // 1.4, 2.5
            throw new RuntimeException('Fail to get parameter.');
        }
        return array_merge($parametersToReturn, $parameters);
    }

    /**
     * @param array $array
     * @return array
     */
    protected function getOnlySeqArray(array $array)
    {
        $arrayToReturn = [];
        foreach ($array as $key => $item) {
            if (is_int($key)) {
                $arrayToReturn[] = $item;
            }
        }
        return $arrayToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, array $arguments)
    {
        return $this->call($this->get($name), $arguments);
    }

    /**
     * @return self
     */
    public function boot()
    {
        foreach ($this->providers as $provider) {
            $provider->boot($this);
        }
        return $this;
    }
}
