<?php
namespace Wandu\Event\Commands\Events;

class NormalPing
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
