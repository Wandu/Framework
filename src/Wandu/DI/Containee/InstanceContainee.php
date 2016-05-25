<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class InstanceContainee extends ContaineeAbstract
{
    public function __construct($value, ContainerInterface $container)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->frozen = true;
        return $this->value;
    }
}
