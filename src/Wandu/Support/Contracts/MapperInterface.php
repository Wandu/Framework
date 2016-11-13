<?php
namespace Wandu\Support\Contracts;

interface MapperInterface
{
    /**
     * @param string $name
     * @return string
     */
    public function map($name);
}
