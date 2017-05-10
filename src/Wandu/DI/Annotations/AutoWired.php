<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use Exception;
use ReflectionClass;
use ReflectionProperty;
use Throwable;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Contracts\PropertyDecoratorInterface;
use Wandu\DI\Descriptor;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class AutoWired implements PropertyDecoratorInterface
{
    /** @var array  */
    static protected $callStack = []; 
    
    /** @Required @var string */
    public $name;

    /**
     * {@inheritdoc}
     */
    public function onBindProperty(
        ReflectionProperty $reflProperty,
        ReflectionClass $reflClass,
        Descriptor $desc,
        ContainerInterface $container
    ) {
        $desc->after(function ($instance) use ($reflProperty, $container) {
            if (in_array($instance, static::$callStack)) {
                return; // return when a circular call is detected.
            }
            array_push(static::$callStack, $instance);
            try {
                $reflProperty->setAccessible(true);
                $reflProperty->setValue($instance, $container->get($this->name));
            } catch (Exception $e) {
                array_pop(static::$callStack);
                throw $e;
            } catch (Throwable $e) {
                array_pop(static::$callStack);
                throw $e;
            }
            array_pop(static::$callStack);
        });
    }
}
