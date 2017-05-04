<?php
namespace Wandu\DI\Contracts;

use ReflectionProperty;
use Wandu\DI\Descriptor;
use Wandu\DI\ContainerInterface;

interface PropertyDecoratorInterface
{
    /**
     * @param \ReflectionProperty $refl
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function beforeCreateProperty(ReflectionProperty $refl, Descriptor $desc, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionProperty $refl
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function afterCreateProperty($target, ReflectionProperty $refl, Descriptor $desc, ContainerInterface $container);
}
