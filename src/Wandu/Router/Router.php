<?php
namespace Wandu\Router;

use IteratorAggregate;

class Router implements IteratorAggregate
{
    /** @var \Wandu\Router\Route[] */
    protected $routes = [];

    /** @var string */
    protected $prefix = '';

    /** @var array */
    protected $middlewares = [];

    /** @var string */
    protected $host;
    
    /**
     * @param string $prefix
     * @param callable $handler
     */
    public function prefix($prefix, callable $handler)
    {
        $beforePrefix = $this->prefix;
        $this->prefix = "{$beforePrefix}/" . ($prefix ?? '');

        call_user_func($handler, $this);

        $this->prefix = $beforePrefix;
    }

    /**
     * @param array|string $middlewares
     * @param callable $handler
     */
    public function middleware($middlewares, callable $handler)
    {
        $middlewares = array_filter((array) $middlewares);
        $beforeMiddlewares = $this->middlewares;
        $this->middlewares = array_merge($beforeMiddlewares, $middlewares);
        call_user_func($handler, $this);
        $this->middlewares = $beforeMiddlewares;
    }

    /**
     * @param array $attributes
     * @param callable $handler
     */
    public function group(array $attributes, callable $handler)
    {
        $this->prefix($attributes['prefix'] ?? '', function () use ($attributes, $handler) {
            $this->middleware($attributes['middleware'] ?? [], function () use ($attributes, $handler) {
                call_user_func($handler, $this);
            });
        });
    }

    /**
     * @param callable $handler
     */
    public function append(callable $handler)
    {
        call_user_func($handler, $this);
    }

    public function resource($className, $except = [], $only = [])
    {
        $this->get('', $className, 'index');
        $this->get('create', $className, 'create');
        $this->post('', $className, 'store');
        $this->get('{id}', $className, 'show');
        $this->put('{id}', $className, 'update');
        $this->delete('{id}', $className, 'destroy');
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function get($path, $className, $methodName = 'index', array $middlewares = [])
    {
        return $this->createRoute(['GET', 'HEAD'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function post($path, $className, $methodName = 'index', array $middlewares = [])
    {
        return $this->createRoute(['POST'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function put($path, $className, $methodName = 'index', array $middlewares = [])
    {
        return $this->createRoute(['PUT'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function delete($path, $className, $methodName = 'index', array $middlewares = [])
    {
        return $this->createRoute(['DELETE'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function options($path, $className, $methodName = 'index', array $middlewares = [])
    {
        return $this->createRoute(['OPTIONS'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function patch($path, $className, $methodName = 'index', array $middlewares = [])
    {
        return $this->createRoute(['PATCH'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function any($path, $className, $methodName = 'index', array $middlewares = [])
    {
        return $this->createRoute([
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'OPTIONS',
            'PATCH'
        ], $path, $className, $methodName, $middlewares);
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
        $path = trim("{$this->prefix}/{$path}", '/');
        while(strpos($path, '//') !== false) {
            $path = str_replace('//', '/', $path);
        }
        $path = '/' . $path;
        $middlewares = array_merge($this->middlewares, $middlewares);
        $route = new Route($className, $methodName, $middlewares);
        $this->routes[] = [$methods, $path, $route];
        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        foreach ($this->routes as $route) {
            yield $route;
        }
    }
}
