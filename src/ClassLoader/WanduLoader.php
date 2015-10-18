<?php
namespace Wandu\Router\ClassLoader;

use Wandu\DI\ContainerInterface;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Middleware\MiddlewareInterface;

class WanduLoader implements ClassLoaderInterface
{
    /** @var string */
    protected $prefixHandler;

    /** @var string */
    protected $prefixMiddleware;

    /**
     * @param ContainerInterface $container
     * @param string $prefixHandler
     * @param string $prefixMiddleware
     */
    public function __construct(ContainerInterface $container, $prefixHandler, $prefixMiddleware)
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
        return [$this->container->create($this->joinNamespace($this->prefixHandler, $class)), $method];
    }

    /**
     * @param string $name
     * @return MiddlewareInterface
     */
    public function mapMiddleware($name)
    {
        return $this->container->create($this->joinNamespace($this->prefixMiddleware, $name));
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
