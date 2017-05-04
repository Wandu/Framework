<?php
namespace Wandu\DI\Contracts;

use ReflectionClass;
use Wandu\DI\Descriptor;
use Wandu\DI\ContainerInterface;

interface ClassDecoratorInterface
{
    /**
     * @param \ReflectionClass $refl
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function beforeCreateClass(ReflectionClass $refl, Descriptor $desc, ContainerInterface $container);

    /**
     * @param object $target
     * @param \ReflectionClass $refl
     * @param \Wandu\DI\Descriptor $desc
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function afterCreateClass($target, ReflectionClass $refl, Descriptor $desc, ContainerInterface $container);
}
