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

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function get($path/*, ...$handlers*/)
    {
        $this->mapWithCreateRoute('GET', func_get_args());
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function post($path/*, ...$handlers*/)
    {
        $this->mapWithCreateRoute('POST', func_get_args());
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function put($path/*, ...$handlers*/)
    {
        $this->mapWithCreateRoute('PUT', func_get_args());
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function delete($path/*, ...$handlers*/)
    {
        $this->mapWithCreateRoute('DELETE', func_get_args());
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function options($path/*, ...$handlers*/)
    {
        $this->mapWithCreateRoute('OPTIONS', func_get_args());
    }

    /**
     * @param string $method
     * @param array $handlers
     */
    public function mapWithCreateRoute($method, $handlers)
    {
        $path = array_shift($handlers);
        $handlers = new HandlerCollection($handlers);

        $this->routes[$method.$path] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handlers,
        ];
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RequestInterface $request)
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $result) {
            foreach ($this->routes as $name => $route) {
                $result->addRoute($route['method'], $route['path'], $name);
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
                return $this->routes[$handler]['handler']->execute($request);
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
