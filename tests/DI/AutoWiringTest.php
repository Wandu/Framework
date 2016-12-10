<?php
namespace Wandu\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit_Framework_TestCase;
use Wandu\DI\Annotations\AutoWired;

class AutoWiringTest extends PHPUnit_Framework_TestCase
{
    public function testAutoWiringViaProperty()
    {
        // do nothing.
        $container = new Container();

        $object = $container->get(AutoWiringTestProperty::class);
        static::assertNull($object->getDepend1());
        static::assertNull($object->getDepend2());

        // autowiring
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiringTestProperty::class)->wire();
        $object = $container->get(AutoWiringTestProperty::class);

        static::assertInstanceOf(AutoWiringTestPropertyDepend::class, $object->getDepend1());
        static::assertNull($object->getDepend2());
    }

    public function testAutoWiringViaConstructor()
    {
        // autowiring
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiringTestConstructor::class)->wire();

        $container->instance('depend.first', $depend1 = new AutoWiringTestPropertyDepend());
        $container->instance('depend.second', $depend2 = new AutoWiringTestPropertyDepend());
        $object = $container->get(AutoWiringTestConstructor::class);

        static::assertSame($depend1, $object->getDepend1());
        static::assertSame($depend2, $object->getDepend2());
    }

    public function testAutoWiringViaMethod()
    {
        // autowiring
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiringTestMethod::class)->wire();

        $container->instance('depend.first', $depend1 = new AutoWiringTestPropertyDepend());
        $container->instance('depend.second', $depend2 = new AutoWiringTestPropertyDepend());
        
        $object = new AutoWiringTestMethod();

        static::assertNull($object->getDepend1());
        static::assertNull($object->getDepend2());

        $container->call([$object, 'setDepend']);

        static::assertSame($depend1, $object->getDepend1());
        static::assertSame($depend2, $object->getDepend2());
    }
    
    public function testAutoWiringEachOther()
    {
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiringTestEachOther1::class)->wire();
        $container->bind(AutoWiringTestEachOther2::class)->wire();

        $object = $container->get(AutoWiringTestEachOther1::class);
        
        static::assertInstanceOf(AutoWiringTestEachOther2::class, $object->getDepend());
        static::assertInstanceOf(AutoWiringTestEachOther1::class, $object->getDepend()->getDepend());

        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiringTestEachOther1::class)->wire();
        $container->bind(AutoWiringTestEachOther2::class)->wire();

        $object = $container->get(AutoWiringTestEachOther2::class);

        static::assertInstanceOf(AutoWiringTestEachOther1::class, $object->getDepend());
        static::assertInstanceOf(AutoWiringTestEachOther2::class, $object->getDepend()->getDepend());
    }
}

class AutoWiringTestPropertyDepend {}
class AutoWiringTestProperty
{
    /**
     * @AutoWired(AutoWiringTestPropertyDepend::class)
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

class AutoWiringTestConstructor
{
    /**
     * @AutoWired(name="depend.first", to="depend1")
     * @AutoWired(name="depend.second", to="depend2")
     * @param $depend1
     * @param $depend2
     */
    public function __construct($depend1, $depend2)
    {
        $this->depend1 = $depend1;
        $this->depend2 = $depend2;
    }

    public function getDepend1()
    {
        return $this->depend1;
    }

    public function getDepend2()
    {
        return $this->depend2;
    }
}

class AutoWiringTestMethod
{
    protected $depend1;
    protected $depend2;

    /**
     * @AutoWired(name="depend.first", to="depend1")
     * @AutoWired(name="depend.second", to="depend2")
     * @param $depend1
     * @param $depend2
     */
    public function setDepend($depend1, $depend2)
    {
        $this->depend1 = $depend1;
        $this->depend2 = $depend2;
    }

    public function getDepend1()
    {
        return $this->depend1;
    }

    public function getDepend2()
    {
        return $this->depend2;
    }
}

class AutoWiringTestEachOther1
{
    /**
     * @AutoWired(AutoWiringTestEachOther2::class)
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

class AutoWiringTestEachOther2
{
    /**
     * @AutoWired(AutoWiringTestEachOther1::class)
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
