<?php
namespace Wandu\Validator\Contracts;

interface Tester
{
    /**
     * @param mixed $data
     * @param mixed $origin
     * @param array $keys
     * @return boolean
     */
    public function test($data, $origin = null, array $keys = []): bool;
}
