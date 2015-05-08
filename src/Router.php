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

    public function get()
    {
        $this->mapWithCreateRoute('GET', func_get_args());
    }

    public function post()
    {
        $this->mapWithCreateRoute('POST', func_get_args());
    }

    public function put()
    {
        $this->mapWithCreateRoute('PUT', func_get_args());
    }

    public function delete()
    {
        $this->mapWithCreateRoute('DELETE', func_get_args());
    }

    public function options()
    {
        $this->mapWithCreateRoute('OPTIONS', func_get_args());
    }

    /**
     * @param string $method
     * @param mixed $args
     */
    public function mapWithCreateRoute($method, $args)
    {
        $path = array_shift($args);
        $handler = array_pop($args);
        $route = new Route($method, $path, $handler, $args);

        $this->routes[] = $route;
    }

    /**
     * @param RequestInterface $request
     * @return array $returnValue
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
