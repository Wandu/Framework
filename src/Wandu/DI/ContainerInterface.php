<?php
namespace Wandu\DI;

use ArrayAccess;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Wandu\DI\Contracts\ContainerFluent;

interface ContainerInterface extends ArrayAccess, PsrContainerInterface
{
    /**
     * @param string $name
     * @param string $package
     * @return mixed
     * @throws \Wandu\DI\Exception\RequirePackageException
     */
    public function assert(string $name, string $package);

    /**
     * @param string[] ...$names
     * @return void
     */
    public function destroy(...$names);

    /**
     * @param string $className
     * @param mixed $value
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function instance(string $className, $value): ContainerFluent;

    /**
     * @param string $name
     * @param string|\Closure $className
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function bind(string $name, $className = null): ContainerFluent;

    /**
     * @param string $alias
     * @param string $target
     * @return void
     */
    public function alias(string $alias, string $target);

    /**
     * @param string $name
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function descriptor(string $name): ContainerFluent;

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
