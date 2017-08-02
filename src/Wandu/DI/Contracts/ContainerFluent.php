<?php
namespace Wandu\DI\Contracts;

interface ContainerFluent
{
    /**
     * @param string $paramName
     * @param mixed $value
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function with(string $paramName, $value): ContainerFluent;

    /**
     * @param array $params
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function withMany(array $params = []): ContainerFluent;
    
    /**
     * @param string $paramName
     * @param mixed $value
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function assign(string $paramName, $value): ContainerFluent;

    /**
     * @param array $params
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function assignMany(array $params = []): ContainerFluent;

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function inject(string $propertyName, $value): ContainerFluent;

    /**
     * @param array $values
     * @return \Wandu\DI\Contracts\ContainerFluent
     */
    public function injectMany(array $values): ContainerFluent;

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
