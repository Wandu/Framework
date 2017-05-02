<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Exception;
use ReflectionProperty;
use Throwable;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Contracts\PropertyDecoratorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class AutoWired implements PropertyDecoratorInterface
{
    /** @Required @var string */
    public $name;

    /** @var string */
    public $to = null;

    public function decoratePropertyBeforeCreate(ReflectionProperty $reflector, ContainerInterface $container)
    {
        // do nothing
    }

    public function decorateProperty($target, ReflectionProperty $reflector, ContainerInterface $container)
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
