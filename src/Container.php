<?php
namespace Wandu\DI;

use ArrayAccess;
use ArrayObject;
use Closure;
use InvalidArgumentException;

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

    /** @var array ref. Pimple */
    protected $frozen = [];

    /** @var array */
    protected $aliases = [];

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
        return isset($this->keys[$name]);
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
     * @param string $name
     */
    public function destroy($name)
    {
        if (isset($this->frozen[$name])) {
            throw new CannotChangeException($name);
        }
        unset($this->keys[$name], $this->closures[$name], $this->instances[$name]);
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
        if ($this->keys[$name] === 'alias') {
            return $this->offsetGet($this->aliases[$name]);
        }
        if (!isset($this->instances[$name])) {
            $this->instances[$name] = call_user_func($this->closures[$name], $this);
        }
        return $this->instances[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function singleton($name, Closure $handler)
    {
        $this->offsetUnset($name);
        $this->keys[$name] = 'singleton';
        $this->closures[$name] = $handler;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function instance($name, $value)
    {
        $this->offsetUnset($name);
        $this->keys[$name] = 'instance';
        $this->instances[$name] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function alias($name, $origin)
    {
        $this->offsetUnset($name);
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
        $provider->register($this);
        return $this;
    }
}
