<?php
namespace Wandu\Router\Contracts;

interface ClassLoaderInterface
{
    /**
     * @param string $name
     * @return object
     */
    public function load($name);
}
