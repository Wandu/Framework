<?php
namespace Wandu\Restifier\Contracts;

interface TransformResource
{
    /**
     * @return ?array
     */
    public function transform();

    /**
     * @param string $name
     * @return ?array
     */
    public function includeAttribute(string $name);
}
