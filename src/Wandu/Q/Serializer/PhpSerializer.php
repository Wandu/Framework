<?php
namespace Wandu\Q\Serializer;

use Wandu\Q\Contracts\SerializerInterface;

class PhpSerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize($value)
    {
        return serialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($string)
    {
        return unserialize($string);
    }
}
