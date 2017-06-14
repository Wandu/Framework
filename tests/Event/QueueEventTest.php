<?php
namespace Wandu\Event;

use Mockery;
use PHPUnit\Framework\TestCase;
use Wandu\DI\Container;
use Wandu\Event\Contracts\ViaQueue;
use Wandu\Event\Listener\ListenHandler;
use Wandu\Q\Queue;

class QueueEventTest extends TestCase 
{
//    public function tearDown()
//    {
//        Mockery::close();
//        static::addToAssertionCount(1);
//    }
//    
//    public function testQueueEvent()
//    {
//        $event = new ViaQueueEventTestEvent();
//        
//        $queue = Mockery::mock(Queue::class);
//        $queue->shouldReceive('enqueue')->with([
//            'method' => 'event:execute',
//            'event' => $event,
//        ])->once();
//        
//        $container = new Container();
//        $container[Queue::class] = $queue;
//        $dispatcher = new Dispatcher($container);
//        
//        $dispatcher->on(ViaQueueEventTestEvent::class, QueueEventTestListener::class);
//        
//        $dispatcher->trigger($event);
//    }
//}
//
//class ViaQueueEventTestEvent implements ViaQueue
//{
//    private $callCount = 0;
//
//    public function call()
//    {
//        $this->callCount++;
//        return $this->callCount;
//    }
//
//    /**
//     * @return int
//     */
//    public function getCallCount()
//    {
//        return $this->callCount;
//    }
//}
//
//class QueueEventTestListener extends ListenHandler
//{
//    protected $lastCallCount = 0;
//
//    public function handle(DispatcherTestEvent $event)
//    {
//        $this->lastCallCount = $event->call();
//    }
//
//    /**
//     * @return null
//     */
//    public function getLastCallCount()
//    {
//        return $this->lastCallCount;
//    }
}
