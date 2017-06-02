<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use Wandu\DI\Container;

class AutoWiredTest extends TestCase
{
    public function testAutoWiringViaProperty()
    {
        // do nothing.
        $container = new Container();

        $object = $container->get(AutoWiredTestProperty::class);
        static::assertNull($object->getDepend1());
        static::assertNull($object->getDepend2());

        // autowiring
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiredTestProperty::class)->wire()->annotated();
        $object = $container->get(AutoWiredTestProperty::class);

        static::assertInstanceOf(AutoWiredTestPropertyDepend::class, $object->getDepend1());
        static::assertNull($object->getDepend2());
    }
    
    public function testAutoWiringEachOther()
    {
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiredTestEachOther1::class)->wire();
        $container->bind(AutoWiredTestEachOther2::class)->wire();

        $object = $container->get(AutoWiredTestEachOther1::class);
        
        static::assertInstanceOf(AutoWiredTestEachOther2::class, $object->getDepend());
        static::assertInstanceOf(AutoWiredTestEachOther1::class, $object->getDepend()->getDepend());

        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiredTestEachOther1::class)->wire();
        $container->bind(AutoWiredTestEachOther2::class)->wire();

        $object = $container->get(AutoWiredTestEachOther2::class);

        static::assertInstanceOf(AutoWiredTestEachOther1::class, $object->getDepend());
        static::assertInstanceOf(AutoWiredTestEachOther2::class, $object->getDepend()->getDepend());
    }
}

class AutoWiredTestPropertyDepend {}
class AutoWiredTestProperty
{
    /**
     * @AutoWired(AutoWiredTestPropertyDepend::class)
     */
    private $depend1;

    private $depend2;

    public function getDepend1()
    {
        return $this->depend1;
    }

    public function getDepend2()
    {
        return $this->depend2;
    }
}

class AutoWiredTestEachOther1
{
    /**
     * @AutoWired(AutoWiredTestEachOther2::class)
     */
    private $depend;

    /**
     * @return mixed
     */
    public function getDepend()
    {
        return $this->depend;
    }
}

class AutoWiredTestEachOther2
{
    /**
     * @AutoWired(AutoWiredTestEachOther1::class)
     */
    private $depend;

    /**
     * @return mixed
     */
    public function getDepend()
    {
        return $this->depend;
    }
}
