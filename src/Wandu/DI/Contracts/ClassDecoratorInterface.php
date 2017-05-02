<?php
namespace Wandu\DI\Contracts;

use ReflectionClass;
use Wandu\DI\ContainerInterface;

interface ClassDecoratorInterface
{
    /**
     * @param \ReflectionClass $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function beforeCreateClass(ReflectionClass $reflector, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionClass $reflector
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function afterCreateClass($target, ReflectionClass $reflector, ContainerInterface $container);
}
