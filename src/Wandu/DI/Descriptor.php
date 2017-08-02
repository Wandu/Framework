<?php
namespace Wandu\DI;

use Wandu\DI\ContainerFluent;

class Descriptor implements ContainerFluent 
{
    /** @var array */
    public $assigns = [];
    
    /** @var array */
    public $wires = [];
    
    /** @var callable[] */
    public $afterHandlers = [];

    /** @var bool */
    public $factory = false;
    
    /** @var bool */
    public $frozen = false;

    /**
     * {@inheritdoc}
     */
    public function assign(string $paramName, $target): ContainerFluent
    {
        $this->assigns[$paramName] = $target;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function assignMany(array $params = []): ContainerFluent
    {
        $this->assigns = $params + $this->assigns;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function wire(string $propertyName, $target): ContainerFluent
    {
        $this->wires[$propertyName] = $target;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function wireMany(array $properties): ContainerFluent
    {
        $this->wires = $properties + $this->wires;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function after(callable $handler): ContainerFluent
    {
        $this->afterHandlers[] = $handler;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function factory(): ContainerFluent
    {
        $this->factory = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function freeze(): ContainerFluent
    {
        $this->frozen = true;
        return $this;
    }
}
