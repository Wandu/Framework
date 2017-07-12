<?php
namespace Wandu\Router;

use IteratorAggregate;
use Wandu\Router\Contracts\Route as RouteInterface;
use Wandu\Router\Contracts\Router as RouterInterface;

class Router implements RouterInterface, IteratorAggregate 
{
    /** @var \Wandu\Router\Router[] */
    protected $routers = [];

    /** @var \Wandu\Router\Route[] */
    protected $routes = [];

    /** @var array */
    protected $status = [
        'domains' => ['@'],
        'prefix' => '',
        'middlewares' => [],
    ];

    /**
     * {@inheritdoc}
     */
    public function prefix(string $prefix, callable $handler)
    {
        $this->group(['prefix' => $prefix, ], $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function domain($domain, callable $handler)
    {
        $this->group(['domain' => $domain, ], $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function middleware($middleware, callable $handler)
    {
        $this->group(['middleware' => $middleware, ], $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function group(array $attributes, callable $handler)
    {
        $router = new Router();
        $status = $this->status;
        if (isset($attributes['prefix'])) {
            $status['prefix'] = "{$status['prefix']}/" . $attributes['prefix'];
        }
        if (isset($attributes['middleware'])) {
            $status['middlewares'] = array_merge($status['middlewares'], array_filter((array)$attributes['middleware']));
        }
        if (isset($attributes['middlewares'])) {
            $status['middlewares'] = array_merge($status['middlewares'], array_filter((array)$attributes['middlewares']));
        }
        if (isset($attributes['domain'])) {
            $status['domains'] = (array) $attributes['domain'];
        }
        if (isset($attributes['domains'])) {
            $status['domains'] = (array) $attributes['domains'];
        }
        $router->status = $status;
        call_user_func($handler, $router);
        $this->routers[] = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function append(callable $handler)
    {
        call_user_func($handler, $this);
    }

    public function resource($className, $except = [], $only = [])
    {
        $this->get('', $className, 'index');
        $this->post('', $className, 'store');
        $this->get(':id', $className, 'show');
        $this->put(':id', $className, 'update');
        $this->patch(':id', $className, 'patch');
        $this->delete(':id', $className, 'destroy');
        // $this->options();
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
        $path = trim("{$this->status['prefix']}/{$path}", '/');
        while(strpos($path, '//') !== false) {
            $path = str_replace('//', '/', $path);
        }
        $path = '/' . $path;
        $route = new Route($className, $methodName, $this->status['middlewares'], $this->status['domains']);
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
        foreach ($this->routers as $router) {
            foreach ($router as $route) {
                yield $route;
            }
        }
    }
}
