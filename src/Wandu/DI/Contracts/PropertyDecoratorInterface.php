<?php
namespace Wandu\DI\Contracts;

use ReflectionProperty;
use Wandu\DI\ContainerInterface;

interface PropertyDecoratorInterface
{
    /**
     * @param object $target
     * @param \ReflectionProperty $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function decorateProperty($target, ReflectionProperty $reflector, ContainerInterface $container);
}
