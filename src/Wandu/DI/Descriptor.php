<?php
namespace Wandu\DI;

use Wandu\DI\Contracts\ContainerFluent;

class Descriptor implements ContainerFluent 
{
    /** @var array */
    public $assigns = [];
    
    /** @var array */
    public $arguments = [];
    
    /** @var array */
    public $wires = [];
    
    /** @var array */
    public $injects = [];

    /** @var callable[] */
    public $afterHandlers = [];

    /** @var bool */
    public $factory = false;
    
    /** @var bool */
    public $frozen = false;

    /**
     * {@inheritdoc}
     */
    public function assign(string $paramName, string $targetName): ContainerFluent
    {
        $this->assigns[$paramName] = $targetName;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function assignMany(array $arguments = []): ContainerFluent
    {
        $this->assigns = $arguments + $this->assigns;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function arguments(array $arguments = []): ContainerFluent
    {
        $this->arguments = $arguments + $this->arguments;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function wire(string $propertyName, string $targetName): ContainerFluent
    {
        $this->wires[$propertyName] = $targetName;
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
    public function inject(string $propertyName, $value): ContainerFluent
    {
        $this->injects[$propertyName] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function injectMany(array $properties): ContainerFluent
    {
        $this->injects = $properties + $this->injects;
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
