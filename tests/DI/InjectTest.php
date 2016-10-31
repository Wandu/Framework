<?php
namespace Wandu\DI;

use Wandu\DI\Annotations\AutoWired;
use Wandu\DI\Exception\CannotInjectException;
use Wandu\DI\Stub\Resolve\AutoResolvedDepend;
use Wandu\DI\Stub\Resolve\DependInterface;
use PHPUnit_Framework_TestCase;
use Wandu\Validator\Rules\EmailValidator;

class InjectTest extends PHPUnit_Framework_TestCase
{
    public function testDirectInjectByPropertyName()
    {
        $container = new Container();

        $example = new InjectTestExample();

        static::assertNull($example->getSomething()); // null
        static::assertNull($example->getOtherthing()); // null

        // inject object
        $container->inject($example, [
            'something' => $something = new AutoResolvedDepend(),
        ]);

        static::assertSame($something, $example->getSomething()); // same

        // inject scalar
        $container->inject($example, [
            'otherthing' => 12341234
        ]);

        static::assertSame($something, $example->getSomething()); // same
        static::assertSame(12341234, $example->getOtherthing()); // same
    }

    public function testDirectInjectByDocClassName()
    {
        $container = new Container();

        $example = new InjectTestExample();

        $container->inject($example, [
            DependInterface::class => $something = new AutoResolvedDepend(),
        ]);

        static::assertSame($something, $example->getSomething()); // same
    }

    public function testAutoInjectWithFail()
    {
        $container = new Container();

        $example = new InjectTestExample();

        try {
            $container->inject($example);
            $this->fail();
        } catch (CannotInjectException $e) {
            static::assertEquals(DependInterface::class, $e->getClass());
            static::assertEquals('something', $e->getProperty());
        }
    }

    public function testAutoInjectWithSuccess()
    {
        $container = new Container();

        $container->instance(DependInterface::class, $something1 = new AutoResolvedDepend);

        $example = new InjectTestExample();
        static::assertNull($example->getSomething());

        $container->inject($example);

        // inject success!
        static::assertSame($something1, $example->getSomething());
    }

    public function testAutoInjectWithDirectInject()
    {
        $container = new Container();

        $container->instance(DependInterface::class, $something1 = new AutoResolvedDepend);

        $example = new InjectTestExample();
        static::assertNull($example->getSomething());

        $container->inject($example, [
            'something' => $something2 = new AutoResolvedDepend(), // this prority bigger than auto resolve's.
        ]);

        // inject success!
        static::assertNotSame($something1, $example->getSomething());
        static::assertSame($something2, $example->getSomething());
    }

    public function testAutoWiring()
    {
        $container = new Container();

        $container->bind(DependInterface::class, AutoResolvedDepend::class);
        $container->bind(InjectTestExample::class)->wire(true);

        static::assertInstanceOf(InjectTestExample::class, $container->get(InjectTestExample::class));
        static::assertSame(
            $container->get(InjectTestExample::class),
            $container->get(InjectTestExample::class)
        );

        $example = $container->get(InjectTestExample::class);
        static::assertInstanceOf(DependInterface::class, $example->getSomething());
    }
    
    public function testEachInject()
    {
        $container = new Container();
        
        $container->bind(TestEachInject1::class)->wire(true);
        $container->bind(TestEachInject2::class)->wire(true);

        $item1 = $container->get(TestEachInject1::class);
        $item2 = $container->get(TestEachInject2::class);

        static::assertInstanceOf(TestEachInject1::class, $item1);
        static::assertInstanceOf(TestEachInject2::class, $item2);

        static::assertInstanceOf(TestEachInject2::class, $item1->getItem());
        static::assertInstanceOf(TestEachInject1::class, $item2->getItem());
        
        // very important!
        static::assertSame($item1, $item2->getItem());
        static::assertSame($item2, $item1->getItem());

        static::assertSame($item1, $item1->getItem()->getItem());
        static::assertSame($item2, $item2->getItem()->getItem());
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
     * @AutoWired()
     * @var \Wandu\DI\TestEachInject1
     */
    protected $item;
    
    public function getItem()
    {
        return $this->item;
    }
}

class InjectTestExample
{
    /**
     * @AutoWired({"foo"})
     * @var \Wandu\DI\Stub\Resolve\DependInterface
     */
    private $something;

    /** @var mixed */
    private $otherthing;

    /**
     * @return mixed
     */
    public function getSomething()
    {
        return $this->something;
    }

    public function getOtherthing()
    {
        return $this->otherthing;
    }
}
