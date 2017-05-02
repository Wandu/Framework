<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContaineeInterface;
use Wandu\DI\ContainerInterface;

abstract class ContaineeAbstract implements ContaineeInterface
{
    /** @var mixed */
    protected $caching;
    
    /** @var bool */
    protected $factoryEnabled = false;
    
    /** @var bool */
    protected $annotatedEnabled = false;
    
    /** @var bool */
    protected $wireEnabled = false;
    
    /** @var bool */
    protected $frozen = false;

    /**
     * {@inheritdoc}
     */
    public function freeze()
    {
        $this->frozen = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFrozen()
    {
        return $this->frozen;
    }

    /**
     * {@inheritdoc}
     */
    public function factory($enabled = true)
    {
        $this->factoryEnabled = $enabled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFactoryEnabled()
    {
        return $this->factoryEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function annotated($enabled = true)
    {
        $this->annotatedEnabled = $enabled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAnnotatedEnabled(): bool
    {
        return $this->annotatedEnabled;
    }
    
    /**
     * {@inheritdoc}
     */
    public function wire($enabled = true)
    {
        $this->annotatedEnabled = true; // if you use autowired, use annotation!
        $this->wireEnabled = $enabled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isWireEnabled()
    {
        return $this->wireEnabled;
    }

    /**
     * @param \Wandu\DI\ContainerInterface $container
     * @return mixed
     */
    abstract public function get(ContainerInterface $container);
}
