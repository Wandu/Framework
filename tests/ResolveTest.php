<?php
namespace Wandu\DI;

use Wandu\DI\Stub\RequiredLibrary;
use Wandu\DI\Stub\RequiredLibraryInterface;
use Wandu\DI\Stub\Resolve\ConstructHasTypeHintAndConfig;
use Wandu\DI\Stub\Resolve\ConstructHasTypeHintOnly;

class ResolveTest extends TestCase
{
    public function testCreateWithNullReferenceException()
    {
        try {
            $this->container->create(ConstructHasTypeHintOnly::class);
            $this->fail();
        } catch (NullReferenceException $e) {
            $this->assertEquals(
                'not exists in this container; ' . RequiredLibraryInterface::class,
                $e->getMessage()
            );
        }
    }

    public function testCreateWithSuccess()
    {
        $this->container->bind(RequiredLibraryInterface::class, RequiredLibrary::class);

        $this->assertInstanceOf(
            ConstructHasTypeHintOnly::class,
            $this->container->create(ConstructHasTypeHintOnly::class)
        );
    }

    public function testCreateWithCannotResolveException()
    {
        $this->container->bind(RequiredLibraryInterface::class, RequiredLibrary::class);

        try {
            $this->container->create(ConstructHasTypeHintAndConfig::class);
            $this->fail();
        } catch (CannotResolveException $e) {
            $this->assertEquals(
                'Auto resolver can resolve the class that use params with type hint; ' . ConstructHasTypeHintAndConfig::class,
                $e->getMessage()
            );
        }
    }

//
//    public function testResolveWithArguments()
//    {
//        $this->container->closure(DepInterface::class, function () {
//            return new DepFoo();
//        });
//
//        $created = $created = $this->container->create(StubClientWithConfig::class, ['config' => 'config string!']);
//
//        $this->assertInstanceOf(StubClientWithConfig::class, $created);
//        $this->assertEquals(['config' => 'config string!'], $created->getConfig());
//    }
//
//    /**
//     * test 6 types of callable
//     */
//    public function testCall()
//    {
//        $this->container->closure(DepInterface::class, function () {
//            return new DepFoo();
//        });
//
//        function stub(DepInterface $dep)
//        {
//            return 'call function';
//        }
//
//        // closure
//        $this->assertEquals('call closure', $this->container->call(function (DepInterface $dep) {
//            return 'call closure';
//        }));
//
//        // function
//        $this->assertEquals('call function', $this->container->call(__NAMESPACE__ . '\\stub'));
//
//        // static method
//        $this->assertInstanceOf(StubClient::class, $this->container->call(StubClient::class . '::create'));
//
//        // array of static
//        $this->assertInstanceOf(StubClient::class, $this->container->call([StubClient::class, 'create']));
//
//        // array of method
//        $this->assertEquals(
//            'call with dependency',
//            $this->container->call([new StubClient(new DepFoo), 'callWithDependency'])
//        );
//
//        // invoker
//        $this->assertEquals(
//            'invoke with',
//            $this->container->call(new Invoker())
//        );
//
//    }
//
//    public function testAutoResolveBind()
//    {
//        $this->container->bind(DepInterface::class, DepFoo::class);
//        $this->container->bind(StubClient::class);
//
//        $this->assertInstanceOf(StubClient::class, $this->container->get(StubClient::class));
//
//        $this->assertSame($this->container->get(StubClient::class), $this->container->get(StubClient::class));
//    }
}
