<?php
namespace Wandu\Router;

use FastRoute\DataGenerator\GroupCountBased as GCBDataGenerator;
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
     * @param callable $handler
     */
    public function prefix($prefix, callable $handler)
    {
        $beforePrefix = $this->prefix;
        $this->prefix = $beforePrefix . $prefix ?: '/';
        call_user_func($handler, $this);
        $this->prefix = $beforePrefix;
    }

    /**
     * @param array|string $middlewares
     * @param callable $handler
     */
    public function middleware($middlewares, callable $handler)
    {
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }
        $beforeMiddlewares = $this->middlewares;
        $this->middlewares = array_merge($beforeMiddlewares, $middlewares);
        $handler($this);
        $this->middlewares = $beforeMiddlewares;
    }

    /**
     * @param array $attributes
     * @param callable $handler
     */
    public function group(array $attributes, callable $handler)
    {
        $beforePrefix = $this->prefix;
        $beforeMiddlewares = $this->middlewares;

        if (isset($attributes['prefix'])) {
            $this->prefix = $beforePrefix . $attributes['prefix'] ?: '/';
        }
        if (isset($attributes['middleware'])) {
            if (!is_array($attributes['middleware'])) {
                $attributes['middleware'] = [$attributes['middleware']];
            }
            $this->middlewares = array_merge($beforeMiddlewares, $attributes['middleware']);
        }

        $handler($this);

        $this->prefix = $beforePrefix;
        $this->middlewares = $beforeMiddlewares;
    }

    /**
     * @param callable $handler
     */
    public function append(callable $handler)
    {
        $handler($this);
    }

    public function resource($className, $except = [], $only = [])
    {
        $this->get('', $className, 'index');
        $this->get('/create', $className, 'create');
        $this->post('', $className, 'store');
        $this->get('/{id}', $className, 'show');
        $this->put('/{id}', $className, 'update');
        $this->delete('/{id}', $className, 'destroy');
    }
    
    /**
     * @param array $methods
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array|string $middlewares
     * @return \Wandu\Router\Route
     */
    public function createRoute(array $methods, $path, $className, $methodName = 'index', array $middlewares = [])
    {
        $path = '/' . trim($this->prefix . $path, '/');
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }
        $middlewares = array_merge($this->middlewares, $middlewares);

        $handler = implode(',', $methods) . $path;
        $this->collector->addRoute($methods, $path, $handler);
        return $this->routes[$handler] = new Route($className, $methodName, $middlewares);
    }
}
