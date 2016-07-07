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
            $object = call_user_func($this->handler, $container);
            return $object;
        }
        if (!isset($this->caching)) {
            $object = call_user_func($this->handler, $container);
            $this->caching = $object;
        }
        return $this->caching;
    }
}
