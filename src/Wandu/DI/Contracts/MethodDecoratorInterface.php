<?php
namespace Wandu\DI\Contracts;

use ReflectionClass;
use ReflectionMethod;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Descriptor;

interface MethodDecoratorInterface
{
    /**
     * @param \ReflectionMethod $reflMethod
     * @param \ReflectionClass $reflClass
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function onBindMethod(ReflectionMethod $reflMethod, ReflectionClass $reflClass, Descriptor $desc, ContainerInterface $container);
}
