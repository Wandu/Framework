<?php
namespace June;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\RequestInterface;
use ArrayAccess;
use ArrayObject;
use RuntimeException;

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

        $this->routes[$method.$path] = $route;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RequestInterface $request)
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $result) {
            foreach ($this->routes as $name => $route) {
                $result->addRoute($route->getMethod(), $route->getPath(), $name);
            }
        });

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new RuntimeException("not found.");
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new RuntimeException("please check your method.");
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                return $this->routes[$handler]->execute($request);
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
