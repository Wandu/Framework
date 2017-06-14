<?php
namespace Wandu\Q;

use Wandu\Q\Contracts\Adapter;
use Wandu\Q\Contracts\Serializer;
use Wandu\Q\Serializer\JsonSerializer;

class Queue
{
    /** @var \Wandu\Q\Contracts\Adapter */
    protected $adapter;

    /** @var \Wandu\Q\Contracts\Serializer */
    protected $serializer;

    /**
     * @param \Wandu\Q\Contracts\Serializer $serializer
     * @param \Wandu\Q\Contracts\Adapter $adapter
     */
    public function __construct(Adapter $adapter, Serializer $serializer = null)
    {
        $this->adapter = $adapter;
        $this->serializer = $serializer ?: new JsonSerializer();
    }

    /**
     * @return void 
     */
    public function flush()
    {
        $this->adapter->flush();
    }

    /**
     * @param mixed $message
     * @return void
     */
    public function send($message)
    {
        $this->adapter->send($this->serializer->serialize($message));
    }

    /**
     * @return \Wandu\Q\Job
     */
    public function receive()
    {
        if ($job = $this->adapter->receive()) {
            return new Job($this->adapter, $this->serializer, $job);
        }
    }
}
