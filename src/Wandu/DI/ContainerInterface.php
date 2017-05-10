<?php
namespace Wandu\DI;

use ArrayAccess;
use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends ArrayAccess, PsrContainerInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * @param string $name
     * @param string $package
     * @return mixed
     * @throws \Wandu\DI\Exception\RequirePackageException
     */
    public function assert(string $name, string $package);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param string[] ...$names
     * @return void
     */
    public function destroy(...$names);

    /**
     * @param string $name
     * @param mixed $value
     * @return \Wandu\DI\Descriptor
     */
    public function instance(string $name, $value): Descriptor;

    /**
     * @param string $name
     * @param callable $handler
     * @return \Wandu\DI\Descriptor
     */
    public function closure(string $name, callable $handler): Descriptor;

    /**
     * @param string $name
     * @param string $className
     * @return \Wandu\DI\Descriptor
     */
    public function bind(string $name, string $className = null): Descriptor;

    /**
     * @param string $alias
     * @param string $target
     * @return void
     */
    public function alias(string $alias, string $target);

    /**
     * @param string $name
     * @return \Wandu\DI\Descriptor
     */
    public function descriptor(string $name): Descriptor;

    /**
     * @param array $arguments
     * @return \Wandu\DI\ContainerInterface
     */
    public function with(array $arguments = []): ContainerInterface;
    
    /**
     * @param string $className
     * @param array $arguments
     * @return object
     */
    public function create(string $className, array $arguments = []);

    /**
     * @param callable $callee
     * @param array $arguments
     * @return mixed
     */
    public function call(callable $callee, array $arguments = []);

    /*
     * for service provider
     */

    /**
     * @param \Wandu\DI\ServiceProviderInterface $provider
     * @return void
     */
    public function register(ServiceProviderInterface $provider);

    /**
     * @return void
     */
    public function boot();
}
