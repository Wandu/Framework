<?php
namespace Wandu\Router\ClassLoader;

use ArrayAccess;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class ArrayAccessLoader implements LoaderInterface
{
    /** @var \ArrayAccess */
    protected $container;

    /**
     * @param \ArrayAccess $container
     */
    public function __construct(ArrayAccess $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function middleware($className): MiddlewareInterface
    {
        if (!isset($this->container[$className])) {
            throw new HandlerNotFoundException($className);
        }
        return $this->container[$className];
    }

    /**
     * {@inheritdoc}
     */
    public function execute($className, $methodName, ServerRequestInterface $request)
    {
        if (!isset($this->container[$className])) {
            throw new HandlerNotFoundException($className, $methodName);
        }
        $object = $this->container[$className];
        if (!method_exists($object, $methodName) && !method_exists($object, '__call')) {
            throw new HandlerNotFoundException($className, $methodName);
        }
        return call_user_func([$object, $methodName], $request);
    }
}
