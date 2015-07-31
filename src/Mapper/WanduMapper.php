<?php
namespace Wandu\Router\Mapper;

use ArrayAccess;
use Wandu\Router\MapperInterface;
use Wandu\Router\Middleware\MiddlewareInterface;

class WanduMapper implements MapperInterface
{
    /** @var string */
    protected $prefixHandler;

    /** @var string */
    protected $prefixMiddleware;

    /**
     * @param ArrayAccess $container
     * @param string $prefixHandler
     * @param string $prefixMiddleware
     */
    public function __construct(ArrayAccess $container, $prefixHandler, $prefixMiddleware)
    {
        $this->container = $container;
        $this->prefixHandler = $prefixHandler;
        $this->prefixMiddleware = $prefixMiddleware;
    }

    /**
     * @param string $name
     * @return callable
     */
    public function mapHandler($name)
    {
        list($method, $class) = explode('@', $name);
        return [$this->container->offsetGet($this->joinNamespace($this->prefixHandler, $class)), $method];
    }

    /**
     * @param string $name
     * @return MiddlewareInterface
     */
    public function mapMiddleware($name)
    {
        return $this->container->offsetGet($this->joinNamespace($this->prefixMiddleware, $name));
    }

    /**
     * @param string $prefix
     * @param string $className
     * @return string
     */
    protected function joinNamespace($prefix, $className)
    {
        if ($className[0] === '\\') {
            return $className;
        }
        return $prefix . '\\' . $className;
    }
}
