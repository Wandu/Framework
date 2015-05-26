<?php
namespace Wandu\DI;

use Mockery;
use PHPUnit_Framework_TestCase;
use stdClass;
use Wandu\Standard\DI\ServiceProviderInterface;

class AutoResolverTest extends PHPUnit_Framework_TestCase
{
    public function testSingleton()
    {
        $resolver = new AutoResolver();

        $resolver->singleton(StubAuto::class);
        $resolver->singleton(StubAutoNeeded::class);

//        try {
//            $resolver->resolve(StubAuto::class);
//            $this->fail();
//        } catch (CannotResolveException $e) {
//            $this->assertEquals('parameter of 2 unmatched array to ?', $e->getMessage());
//        }
//
//        $resolver->parameter(StubAuto::class, 1, []);
//
//        $this->assertInstanceOf(StubAuto::class, $resolver->resolve(StubAuto::class));
//
//        $this->assertSame($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));
    }

//    public function testFactory()
//    {
//        $resolver = new AutoResolver();
//
//        $resolver->factory(StubAuto::class);
//        $resolver->factory(StubAutoNeeded::class);
//
//        $resolver->parameter(StubAuto::class, 1, []);
//
//        $this->assertInstanceOf(StubAuto::class, $resolver->resolve(StubAuto::class));
//
//        $this->assertEquals($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));
//        $this->assertNotSame($resolver->resolve(StubAuto::class), $resolver->resolve(StubAuto::class));
//    }

    public function testCaching()
    {
//        $resolver = new AutoResolver();
//        $resolver->caching(__DIR__.'/resolver.cache', function (AutoResolver $resolver) {
//
//        });
    }
    public function testContainerWithResolver()
    {
        $container = new Container();
        $autoResolver = new AutoResolver();
//        $container->register($autoResolver->asProvider());
    }
}

interface StubAutoNeededInterface {}
class StubAutoNeeded implements StubAutoNeededInterface {}

class StubAuto
{
    public function __construct(StubAutoNeededInterface $need, array $text)
    {

    }
}
