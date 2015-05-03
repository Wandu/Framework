<?php
namespace June;

use June\Route;
use June\Middleware;
use Psr\Http\Message\RequestInterface;

class Router
{
    protected $routes;

    public function __construct()
    {
        $this->routes = array();
    }

    public function count()
    {
        return count($this->routes);
    }

    public function get($path, callable $handler)
    {
        $this->routes[] = [
            'method' => 'GET',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function post($path, callable $handler)
    {
        $this->routes[] = [
            'method' => 'POST',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function put($path, callable $handler)
    {
        $this->routes[] = [
            'method' => 'PUT',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function delete($path, callable $handler)
    {
        $this->routes[] = [
            'method' => 'DELETE',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function options($path, callable $handler)
    {
        $this->routes[] = [
            'method' => 'OPTIONS',
            'controller' => [
                'path' => $path,
                'handler' => $handler
            ]
        ];
    }

    public function dispatch(RequestInterface $request)
    {
        foreach ($this->routes as $route) {
            if (strtolower($request->getMethod()) === strtolower($route['method']) &&
                strtolower($request->getUri()) == strtolower($route['controller']['path'])) {
                return call_user_func($route['controller']['handler']);
            }
        }
    }
}
