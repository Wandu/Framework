<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class InstanceContainee extends ContaineeAbstract
{
    public function __construct($name, $value, ContainerInterface $container)
    {
        $this->name = $name;
        $this->value = $value;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $this->frozen = true;
        return $this->value;
    }
}
