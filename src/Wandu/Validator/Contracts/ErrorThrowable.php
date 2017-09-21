<?php
namespace Wandu\Validator\Contracts;

interface ErrorThrowable
{
    /**
     * @param string $type
     * @param array $keys
     */
    public function throws(string $type, array $keys = []);
}
