<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Exception;
use ReflectionProperty;
use Throwable;
use Wandu\DI\ContaineeInterface;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Contracts\PropertyDecoratorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class AutoWired implements PropertyDecoratorInterface
{
    /** @Required @var string */
    public $name;

    /**
     * {@inheritdoc}
     */
    public function beforeCreateProperty(ReflectionProperty $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function afterCreateProperty($target, ReflectionProperty $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
        static $callStack = [];
        if (in_array($target, $callStack)) {
            return; // return when a circular call is detected.
        }
        array_push($callStack, $target);
        try {
            $reflector->setAccessible(true);
            $reflector->setValue($target, $container->get($this->name));
        } catch (Exception $e) {
            array_pop($callStack);
            throw $e;
        } catch (Throwable $e) {
            array_pop($callStack);
            throw $e;
        }
        array_pop($callStack);
    }
}
