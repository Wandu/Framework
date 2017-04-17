<?php
namespace Wandu\Q\Adapter;

use Wandu\Q\Contracts\AdapterInterface;
use Wandu\Q\Contracts\SerializerInterface;

class NullAdapter implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function enqueue(SerializerInterface $serializer, $payload)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dequeue(SerializerInterface $serializer)
    {
    }
}
