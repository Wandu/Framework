<?php
namespace Wandu\DI\Contracts;

use ReflectionProperty;
use Wandu\DI\ContainerInterface;

interface PropertyDecoratorInterface
{
    /**
     * @param \ReflectionProperty $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function beforeCreateProperty(ReflectionProperty $reflector, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionProperty $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function decorateProperty($target, ReflectionProperty $reflector, ContainerInterface $container);
}
