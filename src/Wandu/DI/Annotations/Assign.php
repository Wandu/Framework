<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use ReflectionMethod;
use Wandu\DI\ContaineeInterface;
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
    public function beforeCreateMethod(ReflectionMethod $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
        $containee->assign([
            $this->target => $container->get($this->name),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function afterCreateMethod($target, ReflectionMethod $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
        // do nothing
    }
}
