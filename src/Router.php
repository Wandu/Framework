<?php
namespace June;

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

    /**
     * @return int
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * @param string $path
     * @param callable $handler
     */
    public function get($path, callable $handler)
    {
        $this->createRoute('GET', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     */
    public function post($path, callable $handler)
    {
        $this->createRoute('POST', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     */
    public function put($path, callable $handler)
    {
        $this->createRoute('PUT', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     */
    public function delete($path, callable $handler)
    {
        $this->createRoute('DELETE', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     */
    public function options($path, callable $handler)
    {
        $this->createRoute('OPTIONS', $path, $handler);
    }

    /**
     * @param string $method
     * @param string $path
     * @param $handler
     */
    public function createRoute($method, $path, $handler)
    {
        $this->routes[] = new Route($method, $path, $handler);
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
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

    /**
     * @param string $name
     * @return ControllerInterface
     */
    public function getController($name)
    {
        return $this->controllers[$name];
    }
}
