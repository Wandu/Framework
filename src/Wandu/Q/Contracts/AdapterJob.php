<?php
namespace Wandu\Q\Contracts;

interface AdapterJob
{
    /**
     * @return string
     */
    public function payload(): string;
}
