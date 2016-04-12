<?php
namespace Wandu\DI;

use ArrayAccess;
use Closure;
use Interop\Container\ContainerInterface as InteropContainerInterface;

interface ContainerInterface extends ArrayAccess, InteropContainerInterface
{
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
     * @return self
     */
    public function instance($name, $value);

    /**
     * @param string $name
     * @param \Closure $handler
     * @return self
     */
    public function closure($name, Closure $handler);

    /**
     * @param string $name
     * @param string $origin
     * @return self
     */
    public function alias($name, $origin);

    /**
     * @param string $name
     * @param \Closure $handler
     * @return self
     */
    public function extend($name, Closure $handler);


    /**
     * @param \Wandu\DI\ServiceProviderInterface $provider
     * @return self
     */
    public function register(ServiceProviderInterface $provider);

    /**
     * @param string $name
     * @param string $class
     * @return self
     */
    public function bind($name, $class = null);

    /**
     * @param string $name
     * @param string $class
     * @return self
     */
    public function wire($name, $class = null);

    /**
     * @param $name
     * @return self
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

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments);
}
