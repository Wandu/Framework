<?php
namespace Wandu\Database\Contracts;

interface MapperInterface
{
    /**
     * @param string $name
     * @return string
     */
    public function map($name);
}
