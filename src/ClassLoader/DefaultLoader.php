<?php
namespace Wandu\Router\ClassLoader;

use Wandu\Router\Contracts\ClassLoaderInterface;

class DefaultLoader implements ClassLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($name)
    {
        return new $name;
    }
}
