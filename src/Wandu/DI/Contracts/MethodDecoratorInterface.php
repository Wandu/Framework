<?php
namespace Wandu\DI\Contracts;

use ReflectionMethod;
use Wandu\DI\ContaineeInterface;
use Wandu\DI\ContainerInterface;

interface MethodDecoratorInterface
{
    /**
     * @param \ReflectionMethod $reflector
     * @param \Wandu\DI\ContaineeInterface $containee
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function beforeCreateMethod(ReflectionMethod $reflector, ContaineeInterface $containee, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionMethod $reflector
     * @param \Wandu\DI\ContaineeInterface $containee
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function afterCreateMethod($target, ReflectionMethod $reflector, ContaineeInterface $containee, ContainerInterface $container);
}
