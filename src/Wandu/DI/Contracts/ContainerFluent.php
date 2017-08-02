<?php
namespace Wandu\DI\Contracts;

interface ContainerFluent
{
    /**
     * @param string $paramName
     * @param string $targetName
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function assign(string $paramName, string $targetName): ContainerFluent;

    /**
     * @param array $params
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function assignMany(array $params = []): ContainerFluent;
    
    /**
     * @param array $arguments
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function arguments(array $arguments = []): ContainerFluent;
    
    /**
     * @param string $propertyName
     * @param string $targetName
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function wire(string $propertyName, string $targetName): ContainerFluent;

    /**
     * @param array $properties
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function wireMany(array $properties): ContainerFluent;

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function inject(string $propertyName, $value): ContainerFluent;

    /**
     * @param array $properties
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function injectMany(array $properties): ContainerFluent;

    /**
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function freeze(): ContainerFluent;

    /**
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function factory(): ContainerFluent;
    
    /**
     * @param callable $handler
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function after(callable $handler): ContainerFluent;
}
