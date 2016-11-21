<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class ClosureContainee extends ContaineeAbstract
{
    /** @var callable */
    protected $handler;
    
    /**
     * @param callable $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ContainerInterface $container)
    {
        $this->frozen = true;
        if ($this->factoryEnabled) {
            return $container->call($this->handler);
        }
        if (!isset($this->caching)) {
            $this->caching = $object = $container->call($this->handler);
        }
        return $this->caching;
    }
}
