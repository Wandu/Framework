<?php
namespace Wandu\DI\Contracts;

use ReflectionClass;
use Wandu\DI\ContainerInterface;

interface ClassDecoratorInterface
{
    /**
     * @param object $target
     * @param \ReflectionClass $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function decorateClass($target, ReflectionClass $reflector, ContainerInterface $container);
}
