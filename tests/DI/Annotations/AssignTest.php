<?php
namespace Wandu\DI\Annotations;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use Wandu\Assertions;
use Wandu\DI\Container;

class AssignTest extends TestCase
{
    use Assertions;
    
    public function testAssignViaConstructor()
    {
        $container = new Container();
        $container->bind(Reader::class, AnnotationReader::class); // add annotation.

        $container->bind(AssignTestConstructor::class)->annotated();

        $container->instance('depend.first', $depend1 = new AssignTestPropertyDepend());
        $container->instance('depend.second', $depend2 = new AssignTestPropertyDepend());

        $object = $container->get(AssignTestConstructor::class);

        static::assertSame($depend1, $object->getDepend1());
        static::assertSame($depend2, $object->getDepend2());
    }
}

class AssignTestPropertyDepend {}
class AssignTestConstructor
{
    /**
     * @Assign(name="depend.first", target="depend1")
     * @Assign(name="depend.second", target="depend2")
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
