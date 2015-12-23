<?php
namespace Wandu\Queue\Serializer;

use Wandu\Queue\Contracts\SerializerInterface;

class JsonSerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize($value)
    {
        return json_encode($value);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($string)
    {
        return json_decode($string, true);
    }
}
