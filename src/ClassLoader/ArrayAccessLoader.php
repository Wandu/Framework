<?php
namespace Wandu\Router\ClassLoader;

use ArrayAccess;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class ArrayAccessLoader implements ClassLoaderInterface
{
    /** @var \ArrayAccess */
    protected $container;

    /**
     * @param \ArrayAccess $container
     */
    public function __construct(ArrayAccess $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load($name)
    {
        if (!isset($this->container[$name])) {
            throw new HandlerNotFoundException($name);
        }
        return $this->container[$name];
    }
}
