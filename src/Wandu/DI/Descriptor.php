<?php
namespace Wandu\DI;

use Wandu\DI\Contracts\ContainerFluent;

class Descriptor implements ContainerFluent 
{
    /** @var array */
    public $values = [];
    
    /** @var array */
    public $assigns = [];
    
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
    public function with(string $paramName, $value): ContainerFluent
    {
        $this->values[$paramName] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withMany(array $params = []): ContainerFluent
    {
        $this->values = $params + $this->values;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function assign(string $paramName, $value): ContainerFluent
    {
        $this->assigns[$paramName] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function assignMany(array $assigns = []): ContainerFluent
    {
        $this->assigns = $assigns + $this->assigns;
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
    public function injectMany(array $values): ContainerFluent
    {
        $this->injects = $values + $this->injects;
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
