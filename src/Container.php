<?php
namespace Wandu\DI;

use ArrayAccess;
use ArrayObject;
use Closure;
use InvalidArgumentException;
use ReflectionClass;

class Container implements ContainerInterface
{
    /** @var ArrayAccess */
    protected $configs;

    /** @var array */
    protected $keys = [];

    /** @var array */
    protected $closures = [];

    /** @var array */
    protected $instances = [];

    /** @var array */
    protected $aliases = [];

    /** @var array */
    protected $dependencies = [];

    /** @var array ref. Pimple */
    protected $frozen = [];

    /**
     * @param ArrayAccess $configs
     */
    public function __construct(ArrayAccess $configs = null)
    {
        if (!isset($configs)) {
            $configs = new ArrayObject();
        }
        $this->configs = $configs;
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
     * @param string $class
     * @param string $method
     * @return object
     */
    public function resolve($class, $method = null)
    {
        return $this->get($class);
    }

    /**
     * @param string $name
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
            $this->dependencies[$name]
        );
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get($name)
    {
        if (!isset($this->keys[$name])) {
            throw new NullReferenceException($name);
        }
        $this->frozen[$name] = true;
        $key = $this->keys[$name];
        if ($key === 'alias') {
            return $this->get($this->aliases[$name]);
        }
        if (!isset($this->instances[$name])) {
            if ($this->keys[$name] === 'closure') {
                $this->instances[$name] = call_user_func($this->closures[$name], $this, $this->configs);
            } elseif ($this->keys[$name] === 'bind') {
                $class = $this->dependencies[$name];
                $refl = new ReflectionClass($class);
                $constructorRefl = $refl->getConstructor();
                $depends = [];
                if ($constructorRefl) {
                    $params = $constructorRefl->getParameters();
                    foreach ($params as $param) {
                        if ($paramRefl = $param->getClass()) {
                            $depends[] = $this->offsetGet($paramRefl->getName());
                        } else {
                            throw new CannotResolveException('Auto resolver can resolve the class that use params with type hint;' . $class);
                        }
                    }
                }
                $this->instances[$name] = $refl->newInstanceArgs($depends);
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
        $this->keys[$name] = 'bind';
        $this->dependencies[$name] = $class;
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
        $provider->register($this, $this->configs);
        return $this;
    }
}
