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
