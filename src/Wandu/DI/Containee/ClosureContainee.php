<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;
use Wandu\Reflection\ReflectionCallable;

class ClosureContainee extends ContaineeAbstract
{
    /** @var callable */
    protected $handler;
    
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    protected function create(ContainerInterface $container)
    {
        return call_user_func_array(
            $this->handler,
            $this->getParameters($container, new ReflectionCallable($this->handler))
        );
    }
}
