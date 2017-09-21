<?php
namespace Wandu\Validator\Contracts;

interface Tester
{
    /**
     * @param mixed $data
     * @param mixed $origin
     * @return boolean
     */
    public function test($data, $origin = null): bool;
}
