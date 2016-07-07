<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class InstanceContainee extends ContaineeAbstract
{
    /** @var mixed */
    protected $source;
    
    /**
     * @param mixed $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ContainerInterface $container)
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
