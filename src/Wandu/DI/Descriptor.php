<?php
namespace Wandu\DI;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Wandu\DI\Contracts\ClassDecoratorInterface;
use Wandu\DI\Contracts\MethodDecoratorInterface;
use Wandu\DI\Contracts\PropertyDecoratorInterface;
use Wandu\DI\Contracts\ResolverInterface;

class Descriptor
{
    /** @var string */
    protected $className;
    
    /** @var \Wandu\DI\Contracts\ResolverInterface */
    protected $resolver;
    
    /** @var mixed */
    public $cache;
    
    /** @var array */
    public $arguments = [];
    
    /** @var bool */
    public $factory = false;
    
    /** @var bool */
    public $annotated = false;
    
    /** @var callable[] */
    public $afters = [];
    
    /** @var bool */
    public $frozen = false;
    
    public function __construct($className = null, ResolverInterface $resolver = null)
    {
        $this->className = $className;
        $this->resolver = $resolver;
    }

    /**
     * @param \Wandu\DI\Contracts\ResolverInterface $resolver
     * @return \Wandu\DI\Descriptor
     */
    public function setResolver(ResolverInterface $resolver): Descriptor
    {
        $this->resolver = $resolver;
        return $this;
    }
    
    /**
     * @param array $arguments
     * @return \Wandu\DI\Descriptor
     */
    public function assign(array $arguments = []): Descriptor
    {
        $this->arguments = $arguments + $this->arguments;
        return $this;
    }

    /**
     * @return \Wandu\DI\Descriptor
     */
    public function freeze(): Descriptor
    {
        $this->frozen = true;
        return $this;
    }

    /**
     * @return \Wandu\DI\Descriptor
     */
    public function factory(): Descriptor
    {
        $this->factory = true;
        return $this;
    }

    /**
     * @return \Wandu\DI\Descriptor
     */
    public function annotated(): Descriptor
    {
        $this->annotated = true;
        return $this;
    }
    
    /**
     * @deprecated
     * 
     * @return \Wandu\DI\Descriptor
     */
    public function wire(): Descriptor
    {
        return $this->annotated();
    }

    /**
     * @param callable $after
     * @return \Wandu\DI\Descriptor
     */
    public function after(callable $after): Descriptor
    {
        $this->afters[] = $after;
        return $this;
    }

    /**
     * @internal
     * @param \Wandu\DI\ContainerInterface $container
     * @return mixed
     */
    public function createInstance(ContainerInterface $container)
    {
        if ($this->factory) {
            if ($this->annotated && $this->className) {
                $reflClass = new ReflectionClass($this->className);
                list($classDecorators, $propertyDecorators, $methodDecorators) = $this->getDecorators($container->get(Reader::class),
                    $reflClass);
                /** @var \Wandu\DI\Contracts\ClassDecoratorInterface $anno */
                foreach ($classDecorators as $anno) {
                    $anno->onBindClass($reflClass, $this, $container);
                }
                /** @var \Wandu\DI\Contracts\PropertyDecoratorInterface $anno */
                foreach ($propertyDecorators as list($anno, $refl)) {
                    $anno->onBindProperty($refl, $reflClass, $this, $container);
                }
                /** @var \Wandu\DI\Contracts\MethodDecoratorInterface $anno */
                foreach ($methodDecorators as list($anno, $refl)) {
                    $anno->onBindMethod($refl, $reflClass, $this, $container);
                }
            }
            $object = $this->resolver->resolve($container, $this->arguments);
            foreach ($this->afters as $after) {
                $result = call_user_func($after, $object);
                if ($result) {
                    $object = $result;
                }
            }
            $this->frozen = true;
            return $object;
        }
        if (!isset($this->cache)) {
            if ($this->annotated && $this->className) {
                $reflClass = new ReflectionClass($this->className);
                list($classDecorators, $propertyDecorators, $methodDecorators) = $this->getDecorators($container->get(Reader::class),
                    $reflClass);
                /** @var \Wandu\DI\Contracts\ClassDecoratorInterface $anno */
                foreach ($classDecorators as $anno) {
                    $anno->onBindClass($reflClass, $this, $container);
                }
                /** @var \Wandu\DI\Contracts\PropertyDecoratorInterface $anno */
                foreach ($propertyDecorators as list($anno, $refl)) {
                    $anno->onBindProperty($refl, $reflClass, $this, $container);
                }
                /** @var \Wandu\DI\Contracts\MethodDecoratorInterface $anno */
                foreach ($methodDecorators as list($anno, $refl)) {
                    $anno->onBindMethod($refl, $reflClass, $this, $container);
                }
            }
            $object = $this->resolver->resolve($container, $this->arguments);
            foreach ($this->afters as $after) {
                $result = call_user_func($after, $object);
                if ($result) {
                    $object = $result;
                }
            }
            $this->cache = $object; 
        }
        $this->frozen = true;
        return $this->cache;
    }

    protected function getDecorators(Reader $reader, ReflectionClass $reflClass)
    {
        $classDecorators = [];
        $propertyDecorators = [];
        $methodDecorators = [];
        foreach ($reader->getClassAnnotations($reflClass) as $classAnnotation) {
            if ($classAnnotation instanceof ClassDecoratorInterface) {
                $classDecorators[] = $classAnnotation;
            }
        }
        foreach ($reflClass->getProperties() as $reflProperty) {
            foreach ($reader->getPropertyAnnotations($reflProperty) as $propertyAnnotation) {
                if ($propertyAnnotation instanceof PropertyDecoratorInterface) {
                    $propertyDecorators[] = [$propertyAnnotation, $reflProperty];
                }
            }
        }
        foreach ($reflClass->getMethods() as $reflMethod) {
            foreach ($reader->getMethodAnnotations($reflMethod) as $methodAnnotation) {
                if ($methodAnnotation instanceof MethodDecoratorInterface) {
                    $methodDecorators[] = [$methodAnnotation, $reflMethod];
                }
            }
        }
        return [
            $classDecorators,
            $propertyDecorators,
            $methodDecorators,
        ];
    }
}
