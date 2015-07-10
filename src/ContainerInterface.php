<?php
namespace Wandu\DI;

use ArrayAccess;
use Closure;

interface ContainerInterface extends ArrayAccess
{
    /**
     * @param string $name
     * @param callable $handler
     * @return self
     */
    public function singleton($name, Closure $handler);

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
     * @param callable $handler
     * @return self
     */
    public function extend($name, Closure $handler);


    /**
     * @param ServiceProviderInterface $provider
     * @return self
     */
    public function register(ServiceProviderInterface $provider);
}
