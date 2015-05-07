<?php
namespace Wandu\DI;

use ArrayAccess;
use Closure;

class Container implements ArrayAccess
{
    /** @var array */
    private $keys = [];

    /** @var array */
    private $closures = [];

    /** @var array */
    private $instances = [];

    /** @var array ref. Pimple */
    private $frozen = [];

    /** @var array */
    private $aliases = [];

    /**
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->keys[$name]);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function offsetGet($name)
    {
        if (!isset($this->keys[$name])) {
            throw new NullReferenceException();
        }
        $this->frozen[$name] = true;
        if ($this->keys[$name] === 'alias') {
            return $this->offsetGet($this->aliases[$name]);
        }
        if ($this->keys[$name] === 'factory') {
            return call_user_func($this->closures[$name], $this);
        }
        if (!isset($this->instances[$name])) {
            $this->instances[$name] = call_user_func($this->closures[$name], $this);
        }
        return $this->instances[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function offsetSet($name, $value)
    {
        $this->instance($name, $value);
    }

    /**
     * @param string $name
     */
    public function offsetUnset($name)
    {
        if (isset($this->frozen[$name])) {
            throw new CannotChangeException();
        }
        unset($this->keys[$name], $this->closures[$name], $this->instances[$name]);
    }

    /**
     * @param string $name
     * @param callable $handler
     */
    public function singleton($name, Closure $handler)
    {
        $this->offsetUnset($name);
        $this->keys[$name] = 'singleton';
        $this->closures[$name] = $handler;
    }

    /**
     * @param string $name
     * @param callable $handler
     */
    public function factory($name, Closure $handler)
    {
        $this->offsetUnset($name);
        $this->keys[$name] = 'factory';
        $this->closures[$name] = $handler;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function instance($name, $value)
    {
        $this->offsetUnset($name);
        $this->keys[$name] = 'instance';
        $this->instances[$name] = $value;
    }

    /**
     * @param string $name
     * @param string $origin
     */
    public function alias($name, $origin)
    {
        $this->offsetUnset($name);
        $this->keys[$name] = 'alias';
        $this->aliases[$name] = $origin;
    }

    /**
     * @param string $name
     * @param callable $handler
     */
    public function extend($name, Closure $handler)
    {
        if (!isset($this->keys[$name])) {
            throw new NullReferenceException();
        }
        if (isset($this->aliases[$name])) {
            $this->extend($this->aliases[$name], $handler);
            return;
        }
        if (isset($this->instances[$name])) {
            $this->instances[$name] = call_user_func($handler, $this->instances[$name]);
        }
        if (isset($this->closures[$name])) {
            $closure = $this->closures[$name];
            $self = $this;
            $this->closures[$name] = function () use ($self, $closure, $handler) {
                return call_user_func($handler, call_user_func($closure, $self));
            };
        }
    }

    /**
     * @param ServiceProviderInterface $provider
     * @return $this
     */
    public function register(ServiceProviderInterface $provider)
    {
        $provider->register($this);
        return $this;
    }
}
