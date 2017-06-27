<?php
namespace Wandu\Router;

use IteratorAggregate;
use Wandu\Router\Contracts\Route as RouteInterface;
use Wandu\Router\Contracts\Router as RouterInterface;

class Router implements IteratorAggregate, RouterInterface 
{
    /** @var \Wandu\Router\Route[] */
    protected $routes = [];

    /** @var string */
    protected $domains = [];
    
    /** @var string */
    protected $prefix = '';

    /** @var array */
    protected $middlewares = [];

    /**
     * @param string $prefix
     * @param callable $handler
     */
    public function prefix(string $prefix, callable $handler)
    {
        $beforePrefix = $this->prefix;
        $this->prefix = "{$beforePrefix}/" . ($prefix ?? '');

        call_user_func($handler, $this);

        $this->prefix = $beforePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function domain($domain, callable $handler)
    {
        $domains = array_filter((array) $domain);
        $beforeDomains = $this->domains;
        $this->domains = array_merge($beforeDomains, $domains);
        call_user_func($handler, $this);
        $this->domains = $beforeDomains;
    }

    /**
     * {@inheritdoc}
     */
    public function middleware($middleware, callable $handler)
    {
        $middlewares = array_filter((array) $middleware);
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
     * {@inheritdoc}
     */
    public function get(string $path, string $className, string $methodName = 'index'): RouteInterface
    {
        return $this->createRoute(['GET', 'HEAD'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $path, string $className, string $methodName = 'index'): RouteInterface
    {
        return $this->createRoute(['POST'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $path, string $className, string $methodName = 'index'): RouteInterface
    {
        return $this->createRoute(['PUT'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $path, string $className, string $methodName = 'index'): RouteInterface
    {
        return $this->createRoute(['DELETE'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function options(string $path, string $className, string $methodName = 'index'): RouteInterface
    {
        return $this->createRoute(['OPTIONS'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function patch(string $path, string $className, string $methodName = 'index'): RouteInterface
    {
        return $this->createRoute(['PATCH'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function any(string $path, string $className, string $methodName = 'index'): RouteInterface
    {
        return $this->createRoute([
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'OPTIONS',
            'PATCH'
        ], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute(array $methods, string $path, string $className, string $methodName = 'index'): RouteInterface
    {
        $path = trim("{$this->prefix}/{$path}", '/');
        while(strpos($path, '//') !== false) {
            $path = str_replace('//', '/', $path);
        }
        $path = '/' . $path;
        $route = new Route($className, $methodName, $this->middlewares, $this->domains);
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
