<?php
namespace Wandu\DI\Containee;

use Closure;
use Wandu\DI\ContainerInterface;

class ClosureContainee extends ContaineeAbstract
{
    /** @var mixed */
    protected $caching;
    
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
        if (!isset($this->caching)) {
            $this->caching = $this->handler->__invoke($this->container);
        }
        return $this->caching;
    }
}
