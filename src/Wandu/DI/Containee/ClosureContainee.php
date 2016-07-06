<?php
namespace Wandu\DI\Containee;

use Closure;
use Wandu\DI\ContainerInterface;

class ClosureContainee extends ContaineeAbstract
{
    /**
     * @param string $name
     * @param \Closure $handler
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct($name, Closure $handler, ContainerInterface $container)
    {
        $this->name = $name;
        $this->handler = $handler;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $this->frozen = true;
        if ($this->factoryEnabled) {
            $object = $this->handler->__invoke($this->container);
            return $object;
        }
        if (!isset($this->caching)) {
            $object = $this->handler->__invoke($this->container);
            $this->caching = $object;
        }
        return $this->caching;
    }
}
