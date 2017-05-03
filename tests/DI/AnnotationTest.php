<?php
namespace Wandu\DI;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Wandu\DI\Contracts\ClassDecoratorInterface;
use Wandu\DI\Contracts\MethodDecoratorInterface;
use Wandu\DI\Contracts\PropertyDecoratorInterface;

class AnnotationTest extends TestCase
{
    public function testSimpleAnnotation()
    {
        $container = new Container();
        $container->instance(Reader::class, new AnnotationReader());
        
        $container->bind(AnnotationTestClass1::class)->annotated();
        
        $obj1 = $container->get(AnnotationTestClass1::class);

        static::assertInstanceOf(AnnotationTestClass1::class, $obj1);

        // class annotation
        static::assertEquals("inserted from class annotation", $obj1->class);
        
        // property annotation
        static::assertEquals("inserted from property annotation", $obj1->getProperty());

        // method annotation
        static::assertEquals(4, $obj1->countOfParams);
    }
}

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class AnnotationTestClassAnnotation implements ClassDecoratorInterface
{
    /** @var string */
    public $name;

    public function beforeCreateClass(ReflectionClass $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function afterCreateClass($object, ReflectionClass $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
        /** @var \Wandu\DI\AnnotationTestClass1 $object */
        $object->class = "{$this->name}";
    }
}

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class AnnotationTestPropertyAnnotation implements PropertyDecoratorInterface
{
    /** @var string */
    public $name;

    public function beforeCreateProperty(ReflectionProperty $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function afterCreateProperty($object, \ReflectionProperty $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
        $reflector->setAccessible(true);
        $reflector->setValue($object, $this->name);
    }
}

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class AnnotationTestMethodAnnotation implements MethodDecoratorInterface
{
    /** @var string */
    public $name;

    public function beforeCreateMethod(ReflectionMethod $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function afterCreateMethod($object, \ReflectionMethod $reflector, ContaineeInterface $containee, ContainerInterface $container)
    {
        $object->countOfParams = $reflector->getNumberOfParameters();
    }
}

/**
 * @AnnotationTestClassAnnotation(name="inserted from class annotation")
 */
class AnnotationTestClass1
{
    public $class;

    /**
     * @AnnotationTestPropertyAnnotation(name="inserted from property annotation")
     */
    private $property;
    
    public $countOfParams;

    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @AnnotationTestMethodAnnotation(name="inserted from method annotation")
     */
    public function method($foo, $bar, $baz, $bazz)
    {
    }
}
