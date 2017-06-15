<?php
namespace Wandu\Event;

use Psr\Container\ContainerInterface;
use Wandu\Event\Contracts\EventEmitter as EventEmitterContract;
use Wandu\Event\Contracts\Listener;
use Wandu\Event\Contracts\ViaQueue;
use Wandu\Event\Listener\CallableListener;
use Wandu\Event\Listener\WorkerListener;
use Wandu\Q\Worker;

class EventEmitter implements EventEmitterContract 
{
    /** @var array */
    protected $listeners = [];

    /** @var \Psr\Container\ContainerInterface */
    protected $container;
    
    /** @var \Wandu\Q\Worker */
    protected $worker;

    public function __construct(array $listeners = [])
    {
        $this->listeners = $listeners;
    }

    /**
     * @param \Psr\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param \Wandu\Q\Worker $worker
     */
    public function setWorker(Worker $worker)
    {
        $this->worker = $worker;
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
        $eventViaQueue = false;
        if (is_object($event)) {
            if ($event instanceof ViaQueue) {
                $eventViaQueue = true;
            }
            $eventName = get_class($event);
            $arguments = [$event];
        } else {
            $eventName = $event;
        }

        /** @var \Wandu\Event\Contracts\Listener $listener */
        foreach ($this->getListeners($eventName) as $listener) {
            if ($eventViaQueue && !$listener instanceof WorkerListener) {
                $listener = new WorkerListener($this->worker, get_class($listener));
            }
            $listener->call($arguments);
        }
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
                $listenerInstance = $this->container->get($listener);
                if ($listenerInstance instanceof Listener) {
                    if ($listenerInstance instanceof ViaQueue) {
                        yield new WorkerListener($this->worker, $listener);
                    } else {
                        yield $listenerInstance;
                    }
                }
            }
        }
    }
}
