<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class BindContainee extends ContaineeAbstract
{
    /** @var object */
    protected $caching;
    
    public function __construct($name, $className, ContainerInterface $container)
    {
        $this->name = $name;
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
