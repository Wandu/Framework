<?php
namespace Wandu\DI;

use Mockery;
use PHPUnit_Framework_TestCase;

class AutoResolverTest extends PHPUnit_Framework_TestCase
{
    public function testAutoResolveConstructor()
    {
        $resolver = new AutoResolver();

        $resolver->bind(StubAutoNeededInterface::class, StubAutoNeeded::class);
        $resolver->bind(StubAuto::class);

        $this->assertInstanceOf(StubAuto::class, $resolver->resolve(StubAuto::class));

        $this->assertEquals($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));
        $this->assertNotSame($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));
    }

//    public function testCaching()
//    {
////        $resolver = new AutoResolver();
////        $resolver->caching(__DIR__.'/resolver.cache', function (AutoResolver $resolver) {
////
////        });
//    }
//    public function testContainerWithResolver()
//    {
//        $container = new Container();
//        $autoResolver = new AutoResolver();
////        $container->register($autoResolver->asProvider());
//    }
}

interface StubAutoNeededInterface {}
class StubAutoNeeded implements StubAutoNeededInterface {}

class StubAuto
{
    public function __construct(StubAutoNeededInterface $need)
    {

    }
}
