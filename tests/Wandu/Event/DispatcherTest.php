<?php
namespace Wandu\Event;

use PHPUnit_Framework_TestCase;
use Mockery;
use Wandu\DI\Container;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function testTriggerByClass()
    {
        $container = new Container();
        $container->instance(DispatcherTestListener::class, $listener = new DispatcherTestListener());

        $dispatcher = new Dispatcher($container);

        $dispatcher->on(DispatcherTestEvent::class, DispatcherTestListener::class);

        // check init event & listener
        $event = new DispatcherTestEvent();
        $this->assertEquals(0, $event->getCallCount());
        $this->assertEquals(0, $listener->getLastCallCount());
        
        $dispatcher->trigger($event);

        $this->assertEquals(1, $event->getCallCount());
        $this->assertEquals(1, $listener->getLastCallCount());

        $dispatcher->trigger($event);
        $dispatcher->trigger($event);
        $dispatcher->trigger($event);

        $this->assertEquals(4, $event->getCallCount());
        $this->assertEquals(4, $listener->getLastCallCount());
    }
}

class DispatcherTestEvent extends Event
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
