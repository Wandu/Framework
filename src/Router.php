<?php
namespace Wandu\Router;

use Closure;
use FastRoute\DataGenerator;
use FastRoute\DataGenerator\GroupCountBased as GCBDataGenerator;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

class Router
{
    use ShortRouterMethods;

    /** @var \FastRoute\RouteCollector */
    protected $collector;

    /** @var \Wandu\Router\Route[] */
    protected $routes = [];

    /** @var string */
    protected $prefix = '';

    /** @var array */
    protected $middlewares = [];

    public function __construct()
    {
        $this->collector = new RouteCollector(new Std(), new GCBDataGenerator());
    }

    /**
     * @return \FastRoute\RouteCollector
     */
    public function getCollector()
    {
        return $this->collector;
    }

    /**
     * @return \Wandu\Router\Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $prefix
     * @param \Closure $handler
     */
    public function prefix($prefix, Closure $handler)
    {
        $beforePrefix = $this->prefix;
        $this->prefix = $beforePrefix . $prefix ?: '/';
        $handler($this);
        $this->prefix = $beforePrefix;
    }

    /**
     * @param array $middlewares
     * @param \Closure $handler
     */
    public function middlewares(array $middlewares, Closure $handler)
    {
        $beforeMiddlewares = $this->middlewares;
        $this->middlewares = array_merge($beforeMiddlewares, $middlewares);
        $handler($this);
        $this->middlewares = $beforeMiddlewares;
    }

    /**
     * @param array $attributes
     * @param \Closure $handler
     */
    public function group(array $attributes, Closure $handler)
    {
        $beforePrefix = $this->prefix;
        $beforeMiddlewares = $this->middlewares;

        if (isset($attributes['prefix'])) {
            $this->prefix = $beforePrefix . $attributes['prefix'] ?: '/';
        }
        if (isset($attributes['middleware'])) {
            $this->middlewares = array_merge($beforeMiddlewares, $attributes['middleware']);
        }

        $handler($this);

        $this->prefix = $beforePrefix;
        $this->middlewares = $beforeMiddlewares;
    }

    /**
     * @param array $methods
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return \Wandu\Router\Route
     */
    public function createRoute(array $methods, $path, $className, $methodName, array $middlewares = [])
    {
        $path = '/' . trim($this->prefix . $path, '/');
        $middlewares = array_merge($this->middlewares, $middlewares);

        $handler = implode(',', $methods) . $path;
        $this->collector->addRoute($methods, $path, $handler);
        return $this->routes[$handler] = new Route($className, $methodName, $middlewares);
    }
}
