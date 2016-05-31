<?php
namespace Wandu\DI;

use Closure;
use Interop\Container\ContainerInterface as InteropContainerInterface;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionObject;
use ReflectionProperty;
use ReflectionException;
use Wandu\DI\Containee\AliasContainee;
use Wandu\DI\Containee\BindContainee;
use Wandu\DI\Containee\ClosureContainee;
use Wandu\DI\Containee\InstanceContainee;
use Wandu\DI\Containee\WireContainee;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotFindParameterException;
use Wandu\DI\Exception\CannotInjectException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\Reflection\ReflectionCallable;

class Container implements ContainerInterface
{
    /** @var \Wandu\DI\ContaineeInterface[] */
    protected $containees = [];
    
    /** @var \Wandu\DI\ServiceProviderInterface[] */
    protected $providers = [];

    /** @var array */
    protected $extenders = [];
    
    /** @var array */
    protected $indexOfAliases = [];

    public function __construct()
    {
        $this->instance(Container::class, $this)->freeze();

        $this->alias(ContainerInterface::class, Container::class)->freeze();
        $this->alias(InteropContainerInterface::class, Container::class)->freeze();
        $this->alias('container', Container::class)->freeze();
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
        return array_key_exists($name, $this->containees) || class_exists($name);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($name)
    {
        if (array_key_exists($name, $this->containees)) {
            if ($this->containees[$name]->isFrozen()) {
                throw new CannotChangeException($name);
            }
        }
        unset($this->containees[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->containees)) {
            if (class_exists($name)) {
                $this->bind($name);
            } else {
                throw new NullReferenceException($name);
            }
        }
        $instance = $this->containees[$name]->get();
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
    public function instance($name, $value)
    {
        $this->destroy($name);
        return $this->containees[$name] = new InstanceContainee($name, $value, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function closure($name, Closure $handler)
    {
        $this->destroy($name);
        return $this->containees[$name] = new ClosureContainee($name, $handler, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function alias($name, $origin)
    {
        $this->destroy($name);
        if (!array_key_exists($origin, $this->indexOfAliases)) {
            $this->indexOfAliases[$origin] = [];
        }
        $this->indexOfAliases[$origin][] = $name;
        return $this->containees[$name] = new AliasContainee($name, $origin, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function bind($name, $class = null)
    {
        $this->destroy($name);
        $this->destroy($class);
        if (!isset($class)) {
            $class = $name;
        }
        $containee = $this->containees[$class] = new BindContainee($name, $class, $this);
        if ($name !== $class) {
            $this->alias($name, $class);
        }
        return $containee;
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
        foreach ($this->providers as $provider) {
            $provider->boot($this);
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
    public function create($class, array $arguments = [])
    {
        $reflectionClass = new ReflectionClass($class);
        $reflectionMethod = $reflectionClass->getConstructor();
        if ($reflectionMethod) {
            try {
                $parameters = $this->getParameters($reflectionMethod, $arguments);
            } catch (CannotResolveException $e) {
                throw $e;
            } catch (CannotFindParameterException $e) {
                throw new CannotResolveException($class, $e->getParameter());
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
            $paramName = $param->getName();
            if (isset($arguments[$paramName])) {
                $parametersToReturn[] = $arguments[$paramName];
                continue;
            }
            try {
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
                throw new CannotFindParameterException($paramName);
            } catch (ReflectionException $e) {
                throw new CannotFindParameterException($paramName);
            }
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
}
