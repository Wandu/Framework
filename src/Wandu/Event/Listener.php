<?php
namespace Wandu\Event;

class Listener
{
    public function call(EventInterface $event)
    {
        if (method_exists($this, 'handle')) {
            $this->handle($event);
        }
    }
}
