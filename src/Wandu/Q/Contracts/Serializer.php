<?php
namespace Wandu\Q\Contracts;

interface Serializer
{
    /**
     * @param mixed $value
     * @return string
     */
    public function serialize($value);

    /**
     * @param string $string
     * @return mixed
     */
    public function unserialize($string);
}
