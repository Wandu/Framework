<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use ReflectionMethod;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Contracts\MethodDecoratorInterface;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Assign implements MethodDecoratorInterface
{
    /** @Required @var string */
    public $name;

    /** @Required @var string */
    public $target;

    public function decorateMethodBeforeCreate(ReflectionMethod $reflector, ContainerInterface $container)
    {
        $containee = $container->containee($reflector->getDeclaringClass()->getName());
        $attributes = $containee->getAssign();
        $attributes[$this->target] = $this->name;
        $containee->assign($attributes);
    }

    public function afterCreateMethod($target, ReflectionMethod $reflector, ContainerInterface $container)
    {
    }
}
