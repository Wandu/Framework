<?php
namespace Wandu\Event;

use Wandu\DI\ContainerInterface;
use Wandu\Event\Contracts\EventEmitter as EventEmitterContract;
use Wandu\Event\Contracts\Listener;
use Wandu\Event\Contracts\ViaQueue;
use Wandu\Event\Listener\CallableListener;
use Wandu\Q\Queue;

class EventEmitter implements EventEmitterContract 
{
    /** @var array */
    protected $listeners = [];

    /** @var \Wandu\DI\ContainerInterface */
    protected $container;
    
    /** @var \Wandu\Q\Queue */
    protected $queue;
    
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function setQueue(Queue $queue)
    {
        $this->queue = $queue;
    }
    
    /**
     * @param string $event
     * @param string|\Closure $listener
     * @return void
     */
    public function on(string $event, $listener)
    {
        if (!array_key_exists($event, $this->listeners)) {
            $this->listeners[$event] = [];
        }
        if (!in_array($listener, $this->listeners[$event])) {
            $this->listeners[$event][] = $listener;
        }
    }

    /**
     * @param string $event
     * @param string|\Closure|\Wandu\Event\Contracts\Listener $listener
     * @return void
     */
    public function off(string $event, $listener = null)
    {
        if (!array_key_exists($event, $this->listeners)) return;
        if ($listener) {
            $key = array_search($listener, $this->listeners[$event]);
            if ($key !== false) {
                array_splice($this->listeners[$event], $key, 1);
            }
            if (count($this->listeners[$event]) === 0) unset($this->listeners[$event]);
        } else {
            unset($this->listeners[$event]);
        }
    }

    /**
     * @param string|object $event
     * @param array ...$arguments
     * @return void
     */
    public function trigger($event, ...$arguments)
    {
        if (is_object($event)) {
            $eventName = get_class($event);
            $arguments = [$event];
        } else {
            $eventName = $event;
        }

        /** @var \Wandu\Event\Contracts\Listener $listener */
        foreach ($this->getListeners($eventName) as $listener) {
            $listener->call($arguments);
        }
//        if ($event instanceof ViaQueue) {
////            if (!$this->container->has(Queue::class)) {
////                // @todo fix error message
////                throw new RuntimeException('cannot load queue.');
////            }
////            $this->container->get(Queue::class)->send([
////                'method' => 'event:execute',
////                'event' => $event,
////            ]);
//        } else {
////            $this->executeListeners($event);
//        }
    }

    /**
     * @param $event
     * @return \Generator|void
     */
    public function getListeners($event)
    {
        if (!isset($this->listeners[$event])) return;
        
        foreach ($this->listeners[$event] as $listener) {
            if ($listener instanceof Listener) {
                yield $listener;
            } elseif (is_callable($listener)) {
                yield new CallableListener($listener);
            } elseif ($this->container && $this->container->has($listener)) {
                $listener = $this->container->get($listener);
                if ($listener instanceof Listener) {
                    if ($listener instanceof ViaQueue) {
                        
                    } else {
                        yield $listener;
                    }
                }
            }
        }
    }
}
