<?php
namespace Wandu\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use Closure;
use RuntimeException;

class Router
{
    use RouterHelperTrait;

    /** @var array */
    protected $routes = [];

    /** @var MapperInterface */
    protected $mapper;

    /** @var array */
    protected $attributes = [
        'prefix' => '',
        'middleware' => [],
    ];

    /**
     * @param MapperInterface $mapper
     */
    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param string|array $attributes
     * @param callable $handler
     */
    public function group($attributes, Closure $handler)
    {
        if (!is_array($attributes)) {
            $attributes = ['prefix' => $attributes];
        }
        $attributes += [
            'prefix' => '',
            'middleware' => []
        ];
        $beforeAttributes = $this->attributes;
        $this->attributes = [
            'prefix' => $this->joinPath($beforeAttributes['prefix'], $attributes['prefix']),
            'middleware' => array_merge($beforeAttributes['middleware'], $attributes['middleware']),
        ];
        call_user_func($handler, $this);
        $this->attributes = $beforeAttributes;
    }

    /**
     * @param string|string[] $methods
     * @param string $path
     * @param callable|string $handler
     * @param array $middlewares
     */
    public function createRoute($methods, $path, $handler, array $middlewares = [])
    {
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        $path = $this->attributes['prefix'] . $path ?: '/';
        $middlewares = array_merge($this->attributes['middleware'], $middlewares);

        foreach ($methods as $method) {
            $this->routes[$method.$path] = [
                'method' => $method,
                'path' => $path,
                'handler' => new Route($handler, $middlewares),
            ];
        }
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
        return $this->routes[$routeInfo[1]]['handler']->execute($request, $this->mapper);
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
