<?php
namespace Wandu\Event;

class Event implements EventInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEventName()
    {
        return static::class;
    }
}
