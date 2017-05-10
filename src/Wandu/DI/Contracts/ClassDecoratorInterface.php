<?php
namespace Wandu\DI\Contracts;

use ReflectionClass;
use Wandu\DI\Descriptor;
use Wandu\DI\ContainerInterface;

interface ClassDecoratorInterface
{
    /**
     * @param \ReflectionClass $reflClass
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function onBindClass(ReflectionClass $reflClass, Descriptor $desc, ContainerInterface $container);
}
