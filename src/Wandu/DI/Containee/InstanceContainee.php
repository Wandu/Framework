<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class InstanceContainee extends ContaineeAbstract
{
    public function __construct($name, $value, ContainerInterface $container)
    {
        $this->name = $name;
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
