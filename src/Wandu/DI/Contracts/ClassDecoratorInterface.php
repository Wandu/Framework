<?php
namespace Wandu\DI\Contracts;

use ReflectionClass;

interface ClassDecoratorInterface
{
    /**
     * @param object $object
     * @param \ReflectionClass $descriptor
     */
    public function decorateClass($object, ReflectionClass $descriptor);
}
