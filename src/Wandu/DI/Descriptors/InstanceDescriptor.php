<?php
namespace Wandu\DI\Descriptors;

use Wandu\DI\ContainerInterface;
use RuntimeException;
use Wandu\DI\Descriptor;

class InstanceDescriptor extends Descriptor
{
    public function __construct($source)
    {
        $this->cache = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function assign(array $attributes = []): Descriptor
    {
        throw new RuntimeException('cannot use assign method, in InstanceDescriptor.');
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance(ContainerInterface $container)
    {
        $this->frozen = true;
        if ($this->factory) {
            return clone $this->cache;
        }
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    protected function create(ContainerInterface $container)
    {
    }
}
