<?php
namespace Wandu\Event;

use PHPUnit_Framework_TestCase;
use Mockery;
use Wandu\DI\Container;
use Wandu\Q\Queue;

class QueueEventTest extends PHPUnit_Framework_TestCase
{
    public function testQueueEvent()
    {
        $event = new QueueEventTestEvent();
        
        $queue = Mockery::mock(Queue::class);
        $queue->shouldReceive('enqueue')->with([
            'method' => 'event:execute',
            'event' => $event,
        ])->once();
        
        $container = new Container();
        $container[Queue::class] = $queue;
        $dispatcher = new Dispatcher($container);
        
        $dispatcher->on(QueueEventTestEvent::class, QueueEventTestListener::class);
        
        $dispatcher->trigger($event);
    }
}

class QueueEventTestEvent extends Event implements QueueEventInterface
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

class QueueEventTestListener extends Listener
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
