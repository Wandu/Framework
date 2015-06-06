<?php
namespace Wandu\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use ArrayAccess;
use ArrayObject;
use Countable;
use Closure;
use RuntimeException;

class Router implements Countable
{
    /** @var array */
    protected $routes = [];

    /** @var ArrayAccess */
    protected $controllers = [];

    /** @var array */
    protected $config;

    /**
     * @param ArrayAccess $controllers
     * @param array $config
     */
    public function __construct(ArrayAccess $controllers = null, array $config = [])
    {
        $this->controllers = isset($controllers) ? $controllers : new ArrayObject();
        $this->config = $config + [
                'prefix' => '',
                'middleware' => []
            ];
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
        $handlers = func_get_args();
        array_shift($handlers);
        $this->createRoute('GET', $path, $handlers);
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function post($path/*, ...$handlers*/)
    {
        $handlers = func_get_args();
        array_shift($handlers);
        $this->createRoute('POST', $path, $handlers);
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function put($path/*, ...$handlers*/)
    {
        $handlers = func_get_args();
        array_shift($handlers);
        $this->createRoute('PUT', $path, $handlers);
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function delete($path/*, ...$handlers*/)
    {
        $handlers = func_get_args();
        array_shift($handlers);
        $this->createRoute('DELETE', $path, $handlers);
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function options($path/*, ...$handlers*/)
    {
        $handlers = func_get_args();
        array_shift($handlers);
        $this->createRoute('OPTIONS', $path, $handlers);
    }

    /**
     * @param string $path
     * @param callable|string ...$handlers
     */
    public function any($path/*, ...$handlers*/)
    {
        $handlers = func_get_args();
        array_shift($handlers);
        $this->createRoute('*', $path, $handlers);
    }

    /**
     * @param string|array $path
     * @param callable $handler
     */
    public function group($config, Closure $handler)
    {
        if (!is_array($config)) {
            $config = ['prefix' => $config];
        }
        $config += [
            'prefix' => '',
            'middleware' => []
        ];
        $beforeConfig = $this->config;
        $this->config = [
            'prefix' => $this->joinPath($beforeConfig['prefix'], $config['prefix']),
            'middleware' => array_merge($beforeConfig['middleware'], $config['middleware']),
        ];
        call_user_func($handler, $this);
        $this->config = $beforeConfig;
    }

    /**
     * @param $method
     * @param $path
     * @param array $handlers
     */
    public function createRoute($method, $path, array $handlers)
    {
        $path = $this->config['prefix'] . $path ?: '/';
        $handlers = array_merge($this->config['middleware'], $handlers);

        $this->routes[$method.$path] = [
            'method' => $method,
            'path' => $path,
            'handler' => new HandlerCollection($handlers),
        ];
    }

    protected function joinPath($path, $pathToJoin)
    {
        return $path . $pathToJoin ?: '/';
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function dispatch(ServerRequestInterface $request)
    {
        $dispatcher = $this->createDispatcher();
        $method = $request->getMethod();
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['_method'])) {
            $method = strtoupper($parsedBody['_method']);
        }
        $routeInfo = $this->runDispatcher($dispatcher, $method, $request->getUri()->getPath());
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        return $this->routes[$routeInfo[1]]['handler']->execute($request, $this->controllers);
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
     * @return array
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
     * @param $controller
     */
    public function setController($name, $controller)
    {
        $this->controllers[$name] = $controller;
    }

    /**
     * @param string $name
     * @return object
     */
    public function getController($name)
    {
        return $this->controllers[$name];
    }

    /**
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
