<?php
namespace Wandu\Event;

interface DispatcherInterface
{
    /**
     * @param string $event
     * @param string|callable $listener
     * @return self
     */
    public function on(string $event, $listener);

    /**
     * @param string $event
     * @param string|callable $listener
     * @return self
     */
    public function off(string $event, $listener = null);

    /**
     * @param \Wandu\Event\EventInterface $event
     */
    public function trigger(EventInterface $event);
}
