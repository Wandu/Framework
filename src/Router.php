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

    public function createRoute($method, $path, $handler)
    {
        $this->routes[] = new Route($method, $path, $handler);
    }

    public function count()
    {
        return count($this->routes);
    }

    public function get($path, callable $handler)
    {
        $this->createRoute('GET', $path, $handler);
    }

    public function post($path, callable $handler)
    {
        $this->createRoute('POST', $path, $handler);
    }

    public function put($path, callable $handler)
    {
        $this->createRoute('PUT', $path, $handler);
    }

    public function delete($path, callable $handler)
    {
        $this->createRoute('DELETE', $path, $handler);
    }

    public function options($path, callable $handler)
    {
        $this->createRoute('OPTIONS', $path, $handler);
    }

    public function dispatch(RequestInterface $request)
    {
        foreach ($this->routes as $route) {
            if ($route->isExecutable($request->getMethod(), $request->getUri()->getPath())) {
                return $route->execute($request);
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
