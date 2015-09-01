<?php
namespace Wandu\DI;

use ArrayAccess;
use Closure;

interface ContainerInterface extends ArrayAccess
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
     * @param \Closure $handler
     * @return self
     */
    public function closure($name, Closure $handler);

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function instance($name, $value);

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
     * @param mixed ...$parameters
     * @return object
     */
    public function create($class);

    /**
     * @param callable $callee
     * @param mixed ...$parameters
     * @return mixed
     */
    public function call(callable $callee);

    /**
     * @param object $object
     * @param array $parameters
     */
    public function inject($object, array $parameters = []);
}
