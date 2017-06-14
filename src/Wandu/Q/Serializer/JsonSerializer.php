<?php
namespace Wandu\Q\Serializer;

use Wandu\Q\Contracts\Serializer;

class JsonSerializer implements Serializer
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
