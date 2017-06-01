<?php
namespace Wandu\Event;

use PHPUnit\Framework\TestCase;
use Wandu\DI\Container;

class DispatcherTest extends TestCase 
{
    public function testTriggerByClass()
    {
        $container = new Container();
        $container->instance(DispatcherTestListener::class, $listener = new DispatcherTestListener());

        $dispatcher = new Dispatcher($container);

        $dispatcher->on(DispatcherTestEvent::class, DispatcherTestListener::class);

        // check init event & listener
        $event = new DispatcherTestEvent();
        static::assertEquals(0, $event->getCallCount());
        static::assertEquals(0, $listener->getLastCallCount());
        
        $dispatcher->trigger($event);

        static::assertEquals(1, $event->getCallCount());
        static::assertEquals(1, $listener->getLastCallCount());

        $dispatcher->trigger($event);
        $dispatcher->trigger($event);
        $dispatcher->trigger($event);

        static::assertEquals(4, $event->getCallCount());
        static::assertEquals(4, $listener->getLastCallCount());
    }
}

class DispatcherTestEvent implements EventInterface
{
    private $callCount = 0;

    public function call()
    {
        $this->callCount++;
        return $this->callCount;
    }

    /**
     * @return int
     */
    public function getCallCount()
    {
        return $this->callCount;
    }
}

class DispatcherTestListener extends Listener
{
    protected $lastCallCount = 0;
    
    public function handle(DispatcherTestEvent $event)
    {
        $this->lastCallCount = $event->call();
    }

    /**
     * @return null
     */
    public function getLastCallCount()
    {
        return $this->lastCallCount;
    }
}
