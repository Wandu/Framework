<?php
namespace Wandu\Event;

class Listener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function call(EventInterface $event)
    {
        if (method_exists($this, 'handle')) {
            $this->handle($event);
        }
    }
}
