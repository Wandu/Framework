<?php
namespace Wandu\Event;

/**
 * @deprecated
 */
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
