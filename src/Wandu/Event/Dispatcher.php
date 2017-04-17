<?php
namespace Wandu\Event;

use Interop\Container\ContainerInterface;
use Wandu\Q\Queue;
use RuntimeException;

class Dispatcher implements DispatcherInterface
{
    /** @var \Interop\Container\ContainerInterface */
    protected $container;

    /** @var array|\Wandu\Event\ListenerInterface[][]|callable[][] */
    protected $listeners = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritdoc}
     */
    public function on(string $event, $listener)
    {
        if (!array_key_exists($event, $this->listeners)) {
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = $listener;
    }

    /**
     * {@inheritdoc}
     */
    public function off(string $event, $listener = null)
    {
        if ($listener) {
            // @todo
        } else {
            $this->listeners[$event] = [];
        }
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
                // @todo fix error message
                throw new RuntimeException('cannot load queue.');
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
     * @return array
     */
    public function executeListeners(EventInterface $event)
    {
        $executedListeners = [];
        $eventName = get_class($event);
        if (!isset($this->listeners[$eventName])) {
            return [];
        }
        foreach ($this->listeners[$eventName] as $listener) {
            if (is_callable($listener)) {
                call_user_func($listener, $event);
                $executedListeners[] = 'callable';
            } elseif (is_string($listener)) {
                $this->container->get($listener)->call($event);
                $executedListeners[] = $listener;
            } elseif ($listener instanceof ListenerInterface) {
                $listener->call($event);
                $executedListeners[] = get_class($listener);
            }
        }
        return $executedListeners;
    }
}
