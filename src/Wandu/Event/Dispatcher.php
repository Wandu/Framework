<?php
namespace Wandu\Event;

use Interop\Container\ContainerInterface;

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
        $this->runListeners(get_class($event), $event);
    }

    /**
     * @param string $eventName
     * @param \Wandu\Event\EventInterface $event
     */
    protected function runListeners($eventName, EventInterface $event)
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }
        foreach ($this->listeners[$eventName] as $listenerName) {
            $this->container->get($listenerName)->handle($event);
        }
    }
}
