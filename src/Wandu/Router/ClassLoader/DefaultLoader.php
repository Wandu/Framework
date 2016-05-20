<?php
namespace Wandu\Router\ClassLoader;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class DefaultLoader implements ClassLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($className)
    {
        if (!class_exists($className)) {
            throw new HandlerNotFoundException($className);
        }
        return new $className;
    }

    /**
     * {@inheritdoc}
     */
    public function call(ServerRequestInterface $request, $object, $methodName)
    {
        if (!method_exists($object, $methodName)) {
            throw new HandlerNotFoundException(get_class($object), $methodName);
        }
        return call_user_func([$object, $methodName], $request);
    }
}
