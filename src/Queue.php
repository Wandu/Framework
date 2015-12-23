<?php
namespace Wandu\Queue;

use Wandu\Queue\Contracts\AdapterInterface;
use Wandu\Queue\Contracts\SerializerInterface;

class Queue
{
    /** @var \Wandu\Queue\Contracts\SerializerInterface */
    protected $serializer;

    /** @var \Wandu\Queue\Contracts\AdapterInterface */
    protected $adapter;

    /**
     * @param \Wandu\Queue\Contracts\SerializerInterface $serializer
     * @param \Wandu\Queue\Contracts\AdapterInterface $adapter
     */
    public function __construct(SerializerInterface $serializer, AdapterInterface $adapter)
    {
        $this->serializer = $serializer;
        $this->adapter = $adapter;
    }

    /**
     * @param mixed $message
     */
    public function enqueue($message)
    {
        $this->adapter->enqueue($this->serializer, $message);
    }

    /**
     * @return \Wandu\Queue\Contracts\JobInterface
     */
    public function dequeue()
    {
        return $this->adapter->dequeue($this->serializer);
    }
}
