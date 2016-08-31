<?php
namespace Wandu\DI;

use Closure;
use Interop\Container\ContainerInterface as InteropContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionObject;
use ReflectionProperty;
use Wandu\DI\Containee\BindContainee;
use Wandu\DI\Containee\ClosureContainee;
use Wandu\DI\Containee\InstanceContainee;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotFindParameterException;
use Wandu\DI\Exception\CannotInjectException;
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
     * @param string $name
     * @return mixed
     */
    public function getRawItem($name)
    {
        return $this->containee($name)->get($this);
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $instance = $this->getRawItem($name);
        if ($this->containees[$name]->isWireEnabled()) {
           $this->inject($instance);
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
    protected function addContainee($name, ContaineeInterface $containee)
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
    public function with(array $arguments = [])
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
    public function create($class, array $arguments = [])
    {
        $seqArguments = static::getSeqArray($arguments);
        $assocArguments = static::getAssocArray($arguments);
        if (count($assocArguments)) {
            return $this->with($assocArguments)->create($class, $seqArguments);
        }
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
        $seqArguments = static::getSeqArray($arguments);
        $assocArguments = static::getAssocArray($arguments);
        if (count($assocArguments)) {
            return $this->with($assocArguments)->call($callee, $seqArguments);
        }
        return call_user_func_array(
            $callee,
            $this->getParameters(new ReflectionCallable($callee), $seqArguments)
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
                        $this->injectProperty($property, $object, $this->getRawItem($propertyClassName));
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
        $parameters = $arguments;
        foreach ($reflectionFunction->getParameters() as $param) {
            // if parameter doesn't have type hint,
            // 1.2. insert remain arguments
            // 1.3. if has default value, insert default value.
            // 1.4. exception

            // if parameter has type hint,
            // 2.2. search in arguments by class name
            // 2.3. search in container by class name
            // 2.4. if has default value, insert default vlue. ( == 1.3)
            // 2.5. exception ( == 1.4)a

            $paramName = $param->getName();
            try {
                $paramClass = $param->getClass();
                if ($paramClass) { // 2.*
                    // 2.2
                    $paramClassName = $paramClass->getName();
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

    /**
     * @param array $array
     * @return array
     */
    protected static function getAssocArray(array $array)
    {
        $arrayToReturn = [];
        foreach ($array as $key => $item) {
            if (!is_int($key)) {
                $arrayToReturn[$key] = $item;
            }
        }
        return $arrayToReturn;
    }
}
