<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class InstanceContainee extends ContaineeAbstract
{
    /** @var mixed */
    protected $source;
    
    /**
     * @param mixed $source
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct($source, ContainerInterface $container)
    {
        $this->source = $source;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $this->frozen = true;
        if (!isset($this->caching)) {
            $this->caching = $this->source;
        }
        if ($this->factoryEnabled) {
            return clone $this->caching;
        }
        return $this->caching;
    }
}
