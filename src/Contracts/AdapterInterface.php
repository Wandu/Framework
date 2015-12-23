<?php
namespace Wandu\Queue\Contracts;

interface AdapterInterface
{
    /**
     * @param \Wandu\Queue\Contracts\SerializerInterface $serializer
     * @param string $payload
     * @return mixed
     */
    public function enqueue(SerializerInterface $serializer, $payload);

    /**
     * @param \Wandu\Queue\Contracts\SerializerInterface $serializer
     * @return \Wandu\Queue\Contracts\JobInterface
     */
    public function dequeue(SerializerInterface $serializer);
}
