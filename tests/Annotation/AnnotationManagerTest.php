<?php
namespace Wandu\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use PHPUnit\Framework\TestCase;

class AnnotationManagerTest extends TestCase 
{
    public function testReader()
    {
        $reader = new AnnotationManager();
        $group = $reader->read(AnnotationManagerTestClass::class);
        
        static::assertEquals([
            $this->createClassAnnotation("class annotation 1"),
            $this->createClassAnnotation("class annotation 2"),
        ], $group->getClassAnnotations());

        static::assertEquals([
            'property1' => [
                $this->createPropertyAnnotation("prop1 annotation 1"),
                $this->createPropertyAnnotation("prop1 annotation 2"),
                $this->createPropertyAnnotation("prop1 annotation 3"),
            ],
            'property2' => [
                $this->createPropertyAnnotation("prop2 annotation 1"),
                $this->createPropertyAnnotation("prop2 annotation 2"),
                $this->createPropertyAnnotation("prop2 annotation 3"),
            ],
        ], $group->getPropertiesAnnotations());

        static::assertEquals([
            $this->createPropertyAnnotation("prop1 annotation 1"),
            $this->createPropertyAnnotation("prop1 annotation 2"),
            $this->createPropertyAnnotation("prop1 annotation 3"),
        ], $group->getPropertyAnnotations("property1"));

        static::assertEquals([
            $this->createPropertyAnnotation("prop2 annotation 1"),
            $this->createPropertyAnnotation("prop2 annotation 2"),
            $this->createPropertyAnnotation("prop2 annotation 3"),
        ], $group->getPropertyAnnotations("property2"));

        static::assertEquals([], $group->getPropertyAnnotations("property3"));

        static::assertEquals([
            'method1' => [
                $this->createMethodAnnotation("method1 annotation 1"),
                $this->createMethodAnnotation("method1 annotation 2"),
                $this->createMethodAnnotation("method1 annotation 3"),
                $this->createMethodAnnotation("method1 annotation 4"),
            ],
            'method2' => [
                $this->createMethodAnnotation("method2 annotation 1"),
                $this->createMethodAnnotation("method2 annotation 2"),
                $this->createMethodAnnotation("method2 annotation 3"),
                $this->createMethodAnnotation("method2 annotation 4"),
            ],
            'method3' => [
                $this->createMethodAnnotation("method3 annotation 1"),
                $this->createMethodAnnotation("method3 annotation 2"),
                $this->createMethodAnnotation("method3 annotation 3"),
                $this->createMethodAnnotation("method3 annotation 4"),
            ],
        ], $group->getMethodsAnnotations());

        static::assertEquals([
            $this->createMethodAnnotation("method1 annotation 1"),
            $this->createMethodAnnotation("method1 annotation 2"),
            $this->createMethodAnnotation("method1 annotation 3"),
            $this->createMethodAnnotation("method1 annotation 4"),
        ], $group->getMethodAnnotations("method1"));

        static::assertEquals([
            $this->createMethodAnnotation("method2 annotation 1"),
            $this->createMethodAnnotation("method2 annotation 2"),
            $this->createMethodAnnotation("method2 annotation 3"),
            $this->createMethodAnnotation("method2 annotation 4"),
        ], $group->getMethodAnnotations("method2"));

        static::assertEquals([
            $this->createMethodAnnotation("method3 annotation 1"),
            $this->createMethodAnnotation("method3 annotation 2"),
            $this->createMethodAnnotation("method3 annotation 3"),
            $this->createMethodAnnotation("method3 annotation 4"),
        ], $group->getMethodAnnotations("method3"));

        static::assertEquals([], $group->getMethodAnnotations("method4"));
    }

    protected function createClassAnnotation($message)
    {
        $annotation = new AnnotationManagerTestClassAnnotation();
        $annotation->message = $message;
        return $annotation;
    }

    protected function createMethodAnnotation($message)
    {
        $annotation = new AnnotationManagerTestMethodAnnotation();
        $annotation->message = $message;
        return $annotation;
    }

    protected function createPropertyAnnotation($message)
    {
        $annotation = new AnnotationManagerTestPropAnnotation();
        $annotation->message = $message;
        return $annotation;
    }
}

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class AnnotationManagerTestClassAnnotation
{
    public $message;
}

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class AnnotationManagerTestMethodAnnotation
{
    public $message;
}

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class AnnotationManagerTestPropAnnotation
{
    public $message;
}

/**
 * @AnnotationManagerTestClassAnnotation(message="class annotation 1")
 * @AnnotationManagerTestClassAnnotation(message="class annotation 2")
 */
class AnnotationManagerTestClass
{
    /**
     * @AnnotationManagerTestPropAnnotation(message="prop1 annotation 1")
     * @AnnotationManagerTestPropAnnotation(message="prop1 annotation 2")
     * @AnnotationManagerTestPropAnnotation(message="prop1 annotation 3")
     */
    protected $property1;

    /**
     * @AnnotationManagerTestPropAnnotation(message="prop2 annotation 1")
     * @AnnotationManagerTestPropAnnotation(message="prop2 annotation 2")
     * @AnnotationManagerTestPropAnnotation(message="prop2 annotation 3")
     */
    protected $property2;

    /**
     * @AnnotationManagerTestMethodAnnotation(message="method1 annotation 1")
     * @AnnotationManagerTestMethodAnnotation(message="method1 annotation 2")
     * @AnnotationManagerTestMethodAnnotation(message="method1 annotation 3")
     * @AnnotationManagerTestMethodAnnotation(message="method1 annotation 4")
     */
    public function method1() {}

    /**
     * @AnnotationManagerTestMethodAnnotation(message="method2 annotation 1")
     * @AnnotationManagerTestMethodAnnotation(message="method2 annotation 2")
     * @AnnotationManagerTestMethodAnnotation(message="method2 annotation 3")
     * @AnnotationManagerTestMethodAnnotation(message="method2 annotation 4")
     */
    public function method2() {}

    /**
     * @AnnotationManagerTestMethodAnnotation(message="method3 annotation 1")
     * @AnnotationManagerTestMethodAnnotation(message="method3 annotation 2")
     * @AnnotationManagerTestMethodAnnotation(message="method3 annotation 3")
     * @AnnotationManagerTestMethodAnnotation(message="method3 annotation 4")
     */
    public function method3() {}
}
