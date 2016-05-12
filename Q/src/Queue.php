<?php
namespace Wandu\Q;

use Wandu\Q\Contracts\AdapterInterface;
use Wandu\Q\Contracts\SerializerInterface;

class Queue
{
    /** @var \Wandu\Q\Contracts\SerializerInterface */
    protected $serializer;

    /** @var \Wandu\Q\Contracts\AdapterInterface */
    protected $adapter;

    /**
     * @param \Wandu\Q\Contracts\SerializerInterface $serializer
     * @param \Wandu\Q\Contracts\AdapterInterface $adapter
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
     * @return \Wandu\Q\Contracts\JobInterface
     */
    public function dequeue()
    {
        return $this->adapter->dequeue($this->serializer);
    }
}
