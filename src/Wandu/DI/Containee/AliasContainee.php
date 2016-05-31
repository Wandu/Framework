<?php
namespace Wandu\DI\Containee;

use Wandu\DI\ContainerInterface;

class AliasContainee extends ContaineeAbstract
{
    public function __construct($name, $destination, ContainerInterface $container)
    {
        $this->name = $name;
        $this->destination = $destination;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $this->frozen = true;
        return $this->container->get($this->destination);
    }
}
