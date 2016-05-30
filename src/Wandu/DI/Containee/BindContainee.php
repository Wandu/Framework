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
    public function create()
    {
        $this->frozen = true;
        if (!isset($this->caching)) {
            $this->caching = $this->container->create($this->className);
        }
        return $this->caching;
    }
}
