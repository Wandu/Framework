<?php
namespace Wandu\DI\Contracts;

use ReflectionProperty;
use Wandu\DI\ContaineeInterface;
use Wandu\DI\ContainerInterface;

interface PropertyDecoratorInterface
{
    /**
     * @param \ReflectionProperty $reflector
     * @param \Wandu\DI\ContaineeInterface $containee
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function beforeCreateProperty(ReflectionProperty $reflector, ContaineeInterface $containee, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionProperty $reflector
     * @param \Wandu\DI\ContaineeInterface $containee
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function afterCreateProperty($target, ReflectionProperty $reflector, ContaineeInterface $containee, ContainerInterface $container);
}
