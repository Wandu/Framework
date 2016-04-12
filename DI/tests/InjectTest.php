<?php
namespace Wandu\DI;

use Mockery;
use Wandu\DI\Exception\CannotInjectException;
use Wandu\DI\Stub\Inject\AutoInjectExample;
use Wandu\DI\Stub\Resolve\AutoResolvedDepend;
use Wandu\DI\Stub\Resolve\DependInterface;

class InjectTest extends TestCase
{
    public function testDirectInjectByPropertyName()
    {
        $example = new AutoInjectExample();

        $this->assertNull($example->getSomething()); // null
        $this->assertNull($example->getOtherthing()); // null

        // inject object
        $this->container->inject($example, [
            'something' => $something = new AutoResolvedDepend(),
        ]);

        $this->assertSame($something, $example->getSomething()); // same

        // inject scalar
        $this->container->inject($example, [
            'something' => null, // Autowired always has some value..
            'otherthing' => 12341234
        ]);

        $this->assertSame(12341234, $example->getOtherthing()); // same
    }

    public function testDirectInjectByDocClassName()
    {
        $example = new AutoInjectExample();

        $this->container->inject($example, [
            DependInterface::class => $something = new AutoResolvedDepend(),
        ]);

        $this->assertSame($something, $example->getSomething()); // same
    }

    public function testAutoInjectWithFail()
    {
        $example = new AutoInjectExample();

        try {
            $this->container->inject($example);
            $this->fail();
        } catch (CannotInjectException $e) {
            $this->assertEquals(DependInterface::class, $e->getClass());
            $this->assertEquals('something', $e->getProperty());
        }
    }

    public function testAutoInjectWithSuccess()
    {
        $this->container->instance(DependInterface::class, $something1 = new AutoResolvedDepend);

        $example = new AutoInjectExample();
        $this->assertNull($example->getSomething());

        $this->container->inject($example);

        // inject success!
        $this->assertSame($something1, $example->getSomething());
    }

    public function testAutoInjectWithDirectInject()
    {
        $this->container->instance(DependInterface::class, $something1 = new AutoResolvedDepend);

        $example = new AutoInjectExample();
        $this->assertNull($example->getSomething());

        $this->container->inject($example, [
            'something' => $something2 = new AutoResolvedDepend(), // this prority bigger than auto resolve's.
        ]);

        // inject success!
        $this->assertNotSame($something1, $example->getSomething());
        $this->assertSame($something2, $example->getSomething());
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
        $this->assertInstanceOf(DependInterface::class, $example->getSomething());
    }
}
