<?php
namespace Wandu\Event;

use Interop\Container\ContainerInterface;
use Wandu\Q\Queue;

class Dispatcher implements DispatcherInterface
{
    /** @var \Interop\Container\ContainerInterface */
    protected $container;

    /** @var array  */
    protected $listeners;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->listeners = [];
    }

    /**
     * @param array $listeners
     */
    public function setListeners(array $listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * {@inheritdoc}
     */
    public function on($eventName, $listenerName)
    {
        if (!array_key_exists($eventName, $this->listeners)) {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][] = $listenerName;
    }

    /**
     * {@inheritdoc}
     */
    public function trigger(EventInterface $event)
    {
        if (!count($this->listeners)) {
            return;
        }
        if ($event instanceof ViaQueue) {
            if (!$this->container->has(Queue::class)) {
                throw new \InvalidArgumentException('Cannot load queue.');
            }
            $this->container->get(Queue::class)->enqueue([
                'method' => 'event:execute',
                'event' => $event,
            ]);
        } else {
            $this->executeListeners($event);
        }
    }

    /**
     * @param \Wandu\Event\EventInterface $event
     */
    public function executeListeners(EventInterface $event)
    {
        $eventName = $event->getEventName();
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        foreach ($this->listeners[$eventName] as $listenerName) {
            $this->container->get($listenerName)->call($event);
        }
    }
}
