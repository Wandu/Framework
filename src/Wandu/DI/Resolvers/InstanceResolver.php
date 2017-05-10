<?php
namespace Wandu\DI\Resolvers;

use Wandu\DI\ContainerInterface;
use Wandu\DI\Contracts\ResolverInterface;

class InstanceResolver implements ResolverInterface
{
    /** @var mixed */
    protected $instance;

    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ContainerInterface $container, array $arguments = [])
    {
        return $this->instance;
    }
}
