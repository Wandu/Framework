<?php
namespace Wandu\DI\Contracts;

use ReflectionMethod;
use Wandu\DI\Descriptor;
use Wandu\DI\ContainerInterface;

interface MethodDecoratorInterface
{
    /**
     * @param \ReflectionMethod $refl
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function beforeCreateMethod(ReflectionMethod $refl, Descriptor $desc, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionMethod $refl
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function afterCreateMethod($target, ReflectionMethod $refl, Descriptor $desc, ContainerInterface $container);
}
