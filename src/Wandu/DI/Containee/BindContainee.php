<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class BindContainee extends ContaineeAbstract
{
    /** @var string */
    protected $className;
    
    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ContainerInterface $container)
    {
        if ($this->factoryEnabled) {
            $object = $container->create($this->className);
            return $object;
        }
        $this->frozen = true;
        if (!isset($this->caching)) {
            $object = $container->create($this->className);
            $this->caching = $object;
        }
        return $this->caching;
    }
}
