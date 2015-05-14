<?php
namespace Jicjjang\June;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
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
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function any($path/*, ...$handlers*/)
    {
        $this->mapWithCreateRoute('*', func_get_args());
    }

    /**
     * @param string $method
     * @param array $handlers
     */
    public function mapWithCreateRoute($method, $handlers)
    {
        $path = array_shift($handlers);
        $this->routes[$method.$path] = [
            'method' => $method,
            'path' => $path,
            'handler' => new HandlerCollection($this->controllers, $handlers),
        ];
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RequestInterface $request)
    {
        $dispatcher = $this->createDispatcher();
        $routeInfo = $this->runDispatcher($dispatcher, $request->getMethod(), $request->getUri()->getPath());
        $request->setArguments($routeInfo[2]);
        return $this->routes[$routeInfo[1]]['handler']->execute($request);
    }

    /**
     * @return Dispatcher
     */
    protected function createDispatcher()
    {
        return \FastRoute\simpleDispatcher(function (RouteCollector $result) {
            foreach ($this->routes as $name => $route) {
                $result->addRoute($route['method'], $route['path'], $name);
            }
        });
    }

    /**
     * @param Dispatcher $dispatcher
     * @param string $method
     * @param string $path
     * @return string
     */
    protected function runDispatcher(Dispatcher $dispatcher, $method, $path)
    {
        $routeInfo = $dispatcher->dispatch($method, $path);
        try {
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    throw new HandlerNotFoundException();
                case Dispatcher::METHOD_NOT_ALLOWED:
                    throw new MethodNotAllowedException();
                case Dispatcher::FOUND:
                    return $routeInfo;
            }
        } catch (RuntimeException $e) {
            if (isset($routeInfo[1]) && in_array('*', $routeInfo[1])) {
                return $this->runDispatcher($dispatcher, '*', $path);
            } else {
                throw $e;
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
