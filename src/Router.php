<?php
namespace June;

use June\Request;
use June\Middleware;

class Router
{
    protected $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    public function count()
    {
        return count($this->routes);
    }

    public function get($path, callable $handler)
    {
        $this->routes = [
            'method' => 'GET',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function post($path, callable $handler)
    {
        $this->routes = [
            'method' => 'POST',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function put($path, callable $handler)
    {
        $this->routes = [
            'method' => 'PUT',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function delete($path, callable $handler)
    {
        $this->routes = [
            'method' => 'DELETE',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function options($path, callable $handler)
    {
        $this->routes = [
            'method' => 'OPTIONS',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function dispatch(Request $request)
    {
        foreach ($this->routes as $route) {
            if ($request->getMethod() == $route['method'] && $request->getPath() == $route['controller']['path']) {
                return call_user_func($route['controller']['handler']);
            }
        }
    }
}
