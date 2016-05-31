<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContaineeInterface;

abstract class ContaineeAbstract implements ContaineeInterface
{
    /** @var string */
    protected $name;
    
    /** @var mixed */
    protected $caching;
    
    /** @var bool */
    protected $factoryEnabled = false;
    
    /** @var bool */
    protected $wireEnabled = false;
    
    /** @var bool */
    protected $frozen = false;

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
    public function freeze()
    {
        $this->frozen = true;
        return $this;
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
     * {@inheritdoc}
     */
    public function wire($enabled = true)
    {
        $this->wireEnabled = $enabled;
        return $this;
    }
}
