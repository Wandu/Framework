<?php
namespace Wandu\DI\Contracts;

use ReflectionMethod;
use Wandu\DI\ContainerInterface;

interface MethodDecoratorInterface
{
    /**
     * @param \ReflectionMethod $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function decorateMethodBeforeCreate(ReflectionMethod $reflector, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionMethod $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function afterCreateMethod($target, ReflectionMethod $reflector, ContainerInterface $container);
}
