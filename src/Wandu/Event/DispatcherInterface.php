<?php
namespace Wandu\Event;

interface DispatcherInterface
{
    /**
     * @param array $listeners
     */
    public function setListeners(array $listeners);

    /**
     * @param string $eventName
     * @param string|callable $listenerName
     * @return self
     */
    public function on($eventName, $listenerName);

    /**
     * @param \Wandu\Event\EventInterface $event
     */
    public function trigger(EventInterface $event);
}
