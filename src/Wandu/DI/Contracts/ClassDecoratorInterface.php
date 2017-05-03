<?php
namespace Wandu\DI\Contracts;

use ReflectionClass;
use Wandu\DI\ContaineeInterface;
use Wandu\DI\ContainerInterface;

interface ClassDecoratorInterface
{
    /**
     * @param \ReflectionClass $reflector
     * @param \Wandu\DI\ContaineeInterface $containee
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function beforeCreateClass(ReflectionClass $reflector, ContaineeInterface $containee, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionClass $reflector
     * @param \Wandu\DI\ContaineeInterface $containee
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function afterCreateClass($target, ReflectionClass $reflector, ContaineeInterface $containee, ContainerInterface $container);
}
