<?php
namespace Wandu\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit_Framework_TestCase;
use Wandu\DI\Annotations\AutoWired;

class AutoWiringTest extends PHPUnit_Framework_TestCase
{
    public function testAutoWiring()
    {
        // do nothing.
        $container = new Container();

        $object = $container->get(AutoWiringTestExample::class);
        static::assertNull($object->getDepend1());
        static::assertNull($object->getDepend2());

        // autowiring
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AutoWiringTestExample::class)->wire();
        $object = $container->get(AutoWiringTestExample::class);

        static::assertInstanceOf(AutoWiringTestExampleDepend::class, $object->getDepend1());
        static::assertNull($object->getDepend2());
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

class AutoWiringTestExampleDepend {}
class AutoWiringTestExample
{
    /**
     * @AutoWired(AutoWiringTestExampleDepend::class)
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
