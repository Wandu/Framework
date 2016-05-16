<?php
namespace Wandu\Event\Events;

use Wandu\Event\Event;
use Wandu\Event\ViaQueue;
use Wandu\Q\Queue;

class Ping extends Event implements ViaQueue
{
    /** @var string */
    protected $message;

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
