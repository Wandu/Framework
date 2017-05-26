<?php
namespace Wandu\Validator\Contracts;

interface TesterInterface
{
    /**
     * @param mixed $data
     * @return boolean
     */
    public function test($data): bool;
}
