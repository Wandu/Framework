<?php
namespace Wandu\DI;

use ArrayAccess;
use Closure;
use Interop\Container\ContainerInterface as InteropContainerInterface;

interface ContainerInterface extends ArrayAccess, InteropContainerInterface
{
    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments);

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param string $name
     */
    public function destroy($name);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param string $name
     * @param mixed $value
     * @return \Wandu\DI\ContaineeInterface
     */
    public function instance($name, $value);

    /**
     * @param string $name
     * @param \Closure $handler
     * @return \Wandu\DI\ContaineeInterface
     */
    public function closure($name, Closure $handler);

    /**
     * @param string|array $name
     * @param string $origin
     * @return \Wandu\DI\ContaineeInterface
     */
    public function alias($name, $origin);

    /**
     * @param string $name
     * @param string $class
     * @return \Wandu\DI\ContaineeInterface
     */
    public function bind($name, $class = null);

    /**
     * @param string $name
     * @param string $class
     * @return \Wandu\DI\ContaineeInterface
     */
    public function wire($name, $class = null);

    /**
     * @param string $name
     * @param \Closure $handler
     */
    public function extend($name, Closure $handler);
    
    /**
     * @param \Wandu\DI\ServiceProviderInterface $provider
     */
    public function register(ServiceProviderInterface $provider);

    /**
     */
    public function boot();

    /**
     * @param $name
     */
    public function freeze($name);

    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function create($class, array $arguments = []);

    /**
     * @param callable $callee
     * @param array $arguments
     * @return mixed
     */
    public function call(callable $callee, array $arguments = []);

    /**
     * @param object $object
     * @param array $properties
     */
    public function inject($object, array $properties = []);
}
