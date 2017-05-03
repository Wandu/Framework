<?php
namespace Wandu\DI;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Wandu\DI\Containee\BindContainee;
use Wandu\DI\Containee\ClosureContainee;
use Wandu\DI\Containee\InstanceContainee;
use Wandu\DI\Exception\CannotChangeException;
use Wandu\DI\Exception\CannotFindParameterException;
use Wandu\DI\Exception\CannotResolveException;
use Wandu\DI\Exception\NullReferenceException;
use Wandu\DI\Exception\RequirePackageException;

class Container implements ContainerInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    public static $instance;
    
    /** @var \Wandu\DI\Containee\ContaineeAbstract[] */
    protected $containees = [];

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
            try {
                $instance = $this->containee($name)->get($this);
            } catch (NullReferenceException $e) {
                if (!class_exists($name)) {
                    throw $e;
                }
                $this->bind($name);
                $instance = $this->containee($name)->get($this);
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
        try {
            return (new BindContainee($className))->assign($arguments)->get($this);
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
            return (new ClosureContainee($callee))->assign($arguments)->get($this);
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
}
