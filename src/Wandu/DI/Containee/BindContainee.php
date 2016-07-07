<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class BindContainee extends ContaineeAbstract
{
    /** @var object */
    protected $caching;

    /**
     * @param $className
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct($className, ContainerInterface $container)
    {
        $this->className = $className;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if ($this->factoryEnabled) {
            $object = $this->container->create($this->className);
            return $object;
        }
        $this->frozen = true;
        if (!isset($this->caching)) {
            $object = $this->container->create($this->className);
            $this->caching = $object;
        }
        return $this->caching;
    }
}
