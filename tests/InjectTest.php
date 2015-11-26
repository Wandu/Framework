<?php
namespace Wandu\DI;

use Mockery;
use Wandu\DI\Exception\CannotInjectException;
use Wandu\DI\Stub\Inject\AutoInjectExample;
use Wandu\DI\Stub\Inject\DirectInjectExample;
use Wandu\DI\Stub\Resolve\AutoResolvedDepend;
use Wandu\DI\Stub\Resolve\DependInterface;

class InjectTest extends TestCase
{
    public function testDirectInject()
    {
        // inject object
        $example1 = new DirectInjectExample();
        $this->assertNull($example1->getSomething()); // null

        $something = new AutoResolvedDepend();
        $this->container->inject($example1, [
            'something' => $something
        ]);

        $this->assertSame($something, $example1->getSomething()); // same

        // inject anything
        $example2 = new DirectInjectExample();
        $this->assertNull($example2->getSomething());

        $this->container->inject($example2, [
            'something' => 12341234
        ]);

        $this->assertSame(12341234, $example2->getSomething()); // same
    }


    public function testAutoInjectWithFail()
    {
        $example = new AutoInjectExample();

        try {
            $this->container->inject($example);
            $this->fail();
        } catch (CannotInjectException $e) {
            $this->assertEquals(AutoInjectExample::class, $e->getClass());
        }
    }

    public function testAutoInjectWithSuccess()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);

        $example = new AutoInjectExample();
        $this->assertNull($example->getRequiredLibrary());

        $this->container->inject($example);

        // inject success!
        $this->assertInstanceOf(DependInterface::class, $example->getRequiredLibrary());
    }

    public function testAutoWiring()
    {
        $this->container->bind(DependInterface::class, AutoResolvedDepend::class);
        $this->container->wire(AutoInjectExample::class); // wire method is like auto inject + bind

        $this->assertInstanceOf(AutoInjectExample::class, $this->container->get(AutoInjectExample::class));
        $this->assertSame(
            $this->container->get(AutoInjectExample::class),
            $this->container->get(AutoInjectExample::class)
        );

        $example = $this->container->get(AutoInjectExample::class);
        $this->assertInstanceOf(DependInterface::class, $example->getRequiredLibrary());
    }
}
