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
        $this->container->bind(AutoInjectExample::class)->wire(true);

        $this->assertInstanceOf(AutoInjectExample::class, $this->container->get(AutoInjectExample::class));
        $this->assertSame(
            $this->container->get(AutoInjectExample::class),
            $this->container->get(AutoInjectExample::class)
        );

        $example = $this->container->get(AutoInjectExample::class);
        $this->assertInstanceOf(DependInterface::class, $example->getSomething());
    }
    
    public function testEachInject()
    {
        $container = new Container();
        
        $container->bind(TestEachInject1::class)->wire(true);
        $container->bind(TestEachInject2::class)->wire(true);

        $item1 = $container->get(TestEachInject1::class);
        $item2 = $container->get(TestEachInject2::class);

        $this->assertInstanceOf(TestEachInject1::class, $item1);
        $this->assertInstanceOf(TestEachInject2::class, $item2);

        $this->assertInstanceOf(TestEachInject2::class, $item1->getItem());
        $this->assertInstanceOf(TestEachInject1::class, $item2->getItem());
        
        // very important!
        $this->assertSame($item1, $item2->getItem());
        $this->assertSame($item2, $item1->getItem());

        $this->assertSame($item1, $item1->getItem()->getItem());
        $this->assertSame($item2, $item2->getItem()->getItem());
    }
}

class TestEachInject1
{
    /**
     * @Autowired
     * @var \Wandu\DI\TestEachInject2
     */
    protected $item;
    
    public function getItem()
    {
        return $this->item;
    }
}

class TestEachInject2
{
    /**
     * @Autowired
     * @var \Wandu\DI\TestEachInject1
     */
    protected $item;
    
    public function getItem()
    {
        return $this->item;
    }
}