<?php
namespace Wandu\DI;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Wandu\DI\Contracts\ResolverInterface;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotFindParameterException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Exception\RequirePackageException;
use Wandu\DI\Resolvers\BindResolver;
use Wandu\DI\Resolvers\CallableResolver;
use Wandu\DI\Resolvers\InstanceResolver;

class Container implements ContainerInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    public static $instance;
    
    /** @var \Wandu\DI\Descriptor[] */
    protected $descriptors = [];
    
    /** @var array */
    protected $caches = [];

    /** @var \Wandu\DI\ServiceProviderInterface[] */
    protected $providers = [];

    /** @var array */
    protected $aliases = [];

    /** @var array */
    protected $aliasIndex = [];
    
    /** @var bool */
    protected $isBooted = false;

    public function __construct()
    {
        $this->caches = [
            Container::class => $this,
            ContainerInterface::class => $this,
            PsrContainerInterface::class => $this,
            'container' => $this,
        ];
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
        $this->caches[Container::class] = $this;
        $this->caches[ContainerInterface::class] = $this;
        $this->caches[PsrContainerInterface::class] = $this;
        $this->caches['container'] = $this;
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
        if (array_key_exists($name, $this->caches)) {
            return $this->caches[$name];
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
        return
            array_key_exists($name, $this->caches) ||
            array_key_exists($name, $this->descriptors) || 
            class_exists($name) ||
            isset($this->aliases[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(...$names)
    {
        foreach ($names as $name) {
            if (array_key_exists($name, $this->caches)) {
                throw new CannotChangeException($name);
            }
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
        return $this->createDescriptor($name, new InstanceResolver($value), class_exists($name) ? $name : null);
    }

    /**
     * {@inheritdoc}
     */
    public function closure(string $name, callable $handler): Descriptor
    {
        return $this->createDescriptor($name, new CallableResolver($handler), class_exists($name) ? $name : null);
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $name, string $className = null): Descriptor
    {
        if (isset($className)) {
            $this->alias($className, $name);
            return $this->createDescriptor($name, new BindResolver($className), $className);
        }
        return $this->createDescriptor($name, new BindResolver($name), $name);
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
    public function create(string $className, array $arguments = [])
    {
        try {
            if (!class_exists($className)) {
                throw new NullReferenceException($className);
            }
            return (new Descriptor($className, new BindResolver($className)))->assign($arguments)->createInstance($this);
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
            return (new Descriptor(null, new CallableResolver($callee)))->assign($arguments)->createInstance($this);
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

    protected function createDescriptor($name, ResolverInterface $resolver, $className = null): Descriptor
    {
        $this->destroy($name);
        return $this->descriptors[$name] = new Descriptor($className, $resolver);
    }
}
