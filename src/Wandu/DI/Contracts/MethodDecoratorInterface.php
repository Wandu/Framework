<?php
namespace Wandu\DI\Contracts;

use ReflectionMethod;
use Wandu\DI\ContainerInterface;

interface MethodDecoratorInterface
{
    /**
     * @param object $target
     * @param \ReflectionMethod $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function decorateMethod($target, ReflectionMethod $reflector, ContainerInterface $container);
}
