<?php
namespace Wandu\DI\Containee;

use Closure;
use Wandu\DI\ContainerInterface;

class ClosureContainee extends ContaineeAbstract
{
    public function __construct(Closure $handler, ContainerInterface $container)
    {
        $this->handler = $handler;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->frozen = true;
        static $caching;
        if (!isset($caching)) {
            $caching = $this->handler->__invoke($this->container);
        }
        return $caching;
    }
}
