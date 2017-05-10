<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use ReflectionClass;
use ReflectionMethod;
use Wandu\DI\Descriptor;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Contracts\MethodDecoratorInterface;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Assign implements MethodDecoratorInterface
{
    /** @Required @var string */
    public $target;

    /** @Required @var string */
    public $name;

    /**
     * {@inheritdoc}
     */
    public function onBindMethod(
        ReflectionMethod $reflMethod,
        ReflectionClass $reflClass,
        Descriptor $desc,
        ContainerInterface $container
    ) {
        $desc->assign([
            $this->target => $container->get($this->name),
        ]);
    }
}
