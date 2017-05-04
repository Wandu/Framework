<?php
namespace Wandu\DI\Descriptors;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Wandu\DI\ContainerInterface;
use Wandu\DI\Descriptor;

class BindDescriptor extends Descriptor
{
    /** @var string */
    protected $className;
    
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    protected function create(ContainerInterface $container)
    {
        $reflClass = new ReflectionClass($this->className);
        if ($this->annotated) {
            list($classDecorators, $propertyDecorators, $methodDecorators) = $this->getDecorators($container->get(Reader::class),
                $reflClass);
            /** @var \Wandu\DI\Contracts\ClassDecoratorInterface $anno */
            foreach ($classDecorators as list($anno, $refl)) {
                $anno->beforeCreateClass($refl, $this, $container);
            }
            /** @var \Wandu\DI\Contracts\PropertyDecoratorInterface $anno */
            foreach ($propertyDecorators as list($anno, $refl)) {
                $anno->beforeCreateProperty($refl, $this, $container);
            }
            /** @var \Wandu\DI\Contracts\MethodDecoratorInterface $anno */
            foreach ($methodDecorators as list($anno, $refl)) {
                $anno->beforeCreateMethod($refl, $this, $container);
            }
        }
        
        $reflectionMethod = $reflClass->getConstructor();
        if (!$reflectionMethod) {
            $instance = $reflClass->newInstance();
        } else {
            $instance = $reflClass->newInstanceArgs(
                $this->getParameters($container, $reflectionMethod)
            );
        }
        if ($this->annotated) {
            /** @var \Wandu\DI\Contracts\ClassDecoratorInterface $anno */
            foreach ($classDecorators as list($anno, $refl)) {
                $anno->afterCreateClass($instance, $refl, $this, $container);
            }
            /** @var \Wandu\DI\Contracts\PropertyDecoratorInterface $anno */
            foreach ($propertyDecorators as list($anno, $refl)) {
                $anno->afterCreateProperty($instance, $refl, $this, $container);
            }
            /** @var \Wandu\DI\Contracts\MethodDecoratorInterface $anno */
            foreach ($methodDecorators as list($anno, $refl)) {
                $anno->afterCreateMethod($instance, $refl, $this, $container);
            }
        }
        return $instance;
    }
}
