<?php
namespace Wandu\DI;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Wandu\DI\Contracts\ClassDecoratorInterface;

class AnnotationTest extends TestCase
{
    public function testBind()
    {
        $container = new Container();
        $container->instance(Reader::class, new AnnotationReader());
        
        $container->bind(AnnotationTestClass1::class)->annotated();
        
        $obj1 = $container->get(AnnotationTestClass1::class);

        static::assertInstanceOf(AnnotationTestClass1::class, $obj1);
        static::assertEquals("inserted test class annotation", $obj1->name);
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

    public function decorateClass($object, ReflectionClass $descriptor)
    {
        /** @var \Wandu\DI\AnnotationTestClass1 $object */
        $object->name = "inserted {$this->name}";
    }
}

/**
 * @AnnotationTestClassAnnotation(name="test class annotation")
 */
class AnnotationTestClass1
{
    public $name;
}
