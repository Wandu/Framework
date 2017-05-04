<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
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
    public function beforeCreateMethod(ReflectionMethod $refl, Descriptor $desc, ContainerInterface $container)
    {
        $desc->assign([
            $this->target => $container->get($this->name),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function afterCreateMethod($target, ReflectionMethod $refl, Descriptor $desc, ContainerInterface $container)
    {
        // do nothing
    }
}
