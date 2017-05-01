<?php
namespace Wandu\Router\ClassLoader;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class DefaultLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function middleware($className): MiddlewareInterface
    {
        if (!class_exists($className)) {
            throw new HandlerNotFoundException($className);
        }
        return new $className;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($className, $methodName, ServerRequestInterface $request)
    {
        if (!method_exists($className, $methodName)) {
            throw new HandlerNotFoundException($className, $methodName);
        }
        return call_user_func([$className, $methodName], $request);
    }
}
