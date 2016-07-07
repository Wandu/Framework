<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class ClosureContainee extends ContaineeAbstract
{
    /**
     * @param callable $handler
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct(callable $handler, ContainerInterface $container)
    {
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
            $object = call_user_func($this->handler, $this->container);
            return $object;
        }
        if (!isset($this->caching)) {
            $object = call_user_func($this->handler, $this->container);
            $this->caching = $object;
        }
        return $this->caching;
    }
}
