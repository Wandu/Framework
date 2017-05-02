<?php
namespace Wandu\DI\Contracts;

use ReflectionProperty;

interface PropertyDecoratorInterface
{
    /**
     * @param object $object
     * @param \ReflectionProperty $descriptor
     */
    public function decorateProperty($object, ReflectionProperty $descriptor);
}
