<?php
namespace Wandu\Q\Serializer;

use Wandu\Q\Contracts\Serializer;

class PhpSerializer implements Serializer
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
