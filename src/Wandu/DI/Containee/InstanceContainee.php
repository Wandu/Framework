<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;
use RuntimeException;

class InstanceContainee extends ContaineeAbstract
{
    /** @var mixed */
    protected $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function assign(array $attributes = [])
    {
        throw new RuntimeException('cannot use assign method, in InstanceContainee.');
    }

    /**
     * {@inheritdoc}
     */
    public function get(ContainerInterface $container)
    {
        $this->frozen = true;
        if ($this->factoryEnabled) {
            return clone $this->source;
        }
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    protected function create(ContainerInterface $container)
    {
    }
}
