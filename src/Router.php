<?php
namespace June;

use June\Route;
use June\Middleware;
use Psr\Http\Message\RequestInterface;
use ArrayAccess;
use ArrayObject;

class Router
{
    /** @var array */
    protected $routes = [];

    /** @var ArrayAccess */
    protected $controllers = [];

    /**
     * @param ArrayAccess $controllers
     */
    public function __construct(ArrayAccess $controllers = null)
    {
        $this->controllers = isset($controllers) ? $controllers : new ArrayObject();
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

    /**
     * @param string $name
     * @param ControllerInterface $controller
     * @return $this
     */
    public function setController($name, ControllerInterface $controller)
    {
        $this->controllers[$name] = $controller;
        return $this;
    }

    public function getController($name)
    {
        return $this->controllers[$name];
    }
}
