<?php
namespace Wandu\DI\Contracts;

use ReflectionMethod;

interface MethodDecoratorInterface
{
    /**
     * @param object $object
     * @param \ReflectionMethod $descriptor
     */
    public function decorateMethod($object, ReflectionMethod $descriptor);
}
