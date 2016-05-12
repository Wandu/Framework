<?php
namespace Wandu\Q\Contracts;

interface AdapterInterface
{
    /**
     * @param \Wandu\Q\Contracts\SerializerInterface $serializer
     * @param string $payload
     * @return mixed
     */
    public function enqueue(SerializerInterface $serializer, $payload);

    /**
     * @param \Wandu\Q\Contracts\SerializerInterface $serializer
     * @return \Wandu\Q\Contracts\JobInterface
     */
    public function dequeue(SerializerInterface $serializer);
}
