<?php
namespace Wandu\DI;

use Closure;
use Wandu\Standard\DI\ContainerInterface;
use Wandu\Standard\DI\ServiceProviderInterface;

class Container implements ContainerInterface
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
            throw new NullReferenceException($name);
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
            throw new CannotChangeException($name);
        }
        unset($this->keys[$name], $this->closures[$name], $this->instances[$name]);
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
    public function factory($name, Closure $handler)
    {
        $this->offsetUnset($name);
        $this->keys[$name] = 'factory';
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
