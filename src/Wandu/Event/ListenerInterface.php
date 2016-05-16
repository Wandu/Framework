<?php
namespace Wandu\Event;

interface ListenerInterface
{
    /**
     * @param \Wandu\Event\EventInterface $event
     */
    public function call(EventInterface $event);
}
