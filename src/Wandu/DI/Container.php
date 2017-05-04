<?php
namespace Wandu\DI;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Wandu\DI\Descriptors\BindDescriptor;
use Wandu\DI\Descriptors\CallableDescriptor;
use Wandu\DI\Descriptors\InstanceDescriptor;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotFindParameterException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Exception\RequirePackageException;

class Container implements ContainerInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    public static $instance;
    
    /** @var \Wandu\DI\Descriptor[] */
    protected $descriptors = [];
    
    /** @var array */
    protected $instances = [];

    /** @var \Wandu\DI\ServiceProviderInterface[] */
    protected $providers = [];

    /** @var array */
    protected $extenders = [];

    /** @var array */
    protected $aliases = [];

    /** @var array */
    protected $aliasIndex = [];
    
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
            $this->descriptors[Container::class],
            $this->descriptors[ContainerInterface::class],
            $this->descriptors[PsrContainerInterface::class],
            $this->descriptors['container']
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
        while (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }
        try {
            try {
                $instance = $this->descriptor($name)->createInstance($this);
            } catch (NullReferenceException $e) {
                if (!class_exists($name)) {
                    throw $e;
                }
                $this->bind($name);
                $instance = $this->descriptor($name)->createInstance($this);
            }
            foreach ($this->getExtenders($name) as $extender) {
                $instance = $extender->__invoke($instance);
            }
        } catch (CannotFindParameterException $e) {
            throw new CannotResolveException($name, $e->getParameter());
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
        return array_key_exists($name, $this->descriptors) || class_exists($name) || isset($this->aliases[$name]);
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
            unset($this->descriptors[$name]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function instance(string $name, $value): Descriptor
    {
        return $this->createDescriptor($name, new InstanceDescriptor($value));
    }

    /**
     * {@inheritdoc}
     */
    public function closure(string $name, callable $handler): Descriptor
    {
        return $this->createDescriptor($name, new CallableDescriptor($handler));
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $name, string $className = null): Descriptor
    {
        if (isset($className)) {
            $this->alias($className, $name);
            return $this->createDescriptor($name, new BindDescriptor($className));
        }
        return $this->createDescriptor($name, new BindDescriptor($name));
    }

    /**
     * {@inheritdoc}
     */
    public function alias(string $alias, string $target)
    {
        if (!array_key_exists($target, $this->aliasIndex)) {
            $this->aliasIndex[$target] = [];
        }
        $this->aliasIndex[$target][] = $alias;
        $this->aliases[$alias] = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function descriptor(string $name): Descriptor
    {
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
            if ($argument instanceof Descriptor) {
                $new->createDescriptor($name, $argument);
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
        try {
            return (new BindDescriptor($className))->assign($arguments)->createInstance($this);
        } catch (CannotFindParameterException $e) {
            throw new CannotResolveException($className, $e->getParameter());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function call(callable $callee, array $arguments = [])
    {
        try {
            return (new CallableDescriptor($callee))->assign($arguments)->createInstance($this);
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
        if (isset($this->aliasIndex[$name])) {
            foreach ($this->aliasIndex[$name] as $aliasName) {
                $extenders = array_merge($extenders, $this->getExtenders($aliasName));
            }
        }
        return $extenders;
    }

    /**
     * @param string $name
     * @param \Wandu\DI\Descriptor $containee
     * @return \Wandu\DI\Descriptor
     */
    protected function createDescriptor($name, Descriptor $containee): Descriptor
    {
        $this->destroy($name);
        return $this->descriptors[$name] = $containee;
    }
}
