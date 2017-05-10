<?php
namespace Wandu\DI\Contracts;

use ReflectionClass;
use ReflectionProperty;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Descriptor;

interface PropertyDecoratorInterface
{
    /**
     * @param \ReflectionProperty $reflProperty
     * @param \ReflectionClass $reflClass
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function onBindProperty(ReflectionProperty $reflProperty, ReflectionClass $reflClass, Descriptor $desc, ContainerInterface $container);
}
