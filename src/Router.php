<?php
namespace Wandu\Router;

use Closure;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\Exception\MethodNotAllowedException;

class Router
{
    use ShortRouterMethods;

    /** @var \Wandu\Router\Route[] */
    protected $routes = [];

    /** @var array */
    protected $dispatches = [];

    /** @var array */
    protected $attributes = [
        'prefix' => '',
        'middleware' => [],
    ];

    /**
     * @param array $attributes
     * @param \Closure $handler
     */
    public function group(array $attributes, Closure $handler)
    {
        $beforeAttributes = $this->attributes;
        if (isset($attributes['prefix'])) {
            $this->attributes['prefix'] = $beforeAttributes['prefix'] . $attributes['prefix'] ?: '/';
        }
        if (isset($attributes['middleware'])) {
            $this->attributes['middleware'] = array_merge($beforeAttributes['middleware'], $attributes['middleware']);
        }
        $handler($this);
        $this->attributes = $beforeAttributes;
    }

    /**
     * @param array $methods
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return \Wandu\Router\Route
     */
    public function createRoute(array $methods, $path, $className, $methodName, array $middlewares = [])
    {
        $path = $this->attributes['prefix'] . $path ?: '/';
        $middlewares = array_merge($this->attributes['middleware'], $middlewares);

        $handler = implode(',', $methods) . $path;
        $this->dispatches[] = [
            'methods' => $methods,
            'path' => $path,
            'handler' => $handler,
        ] ;
        return $this->routes[$handler] = new Route($className, $methodName, $middlewares);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     */
    public function dispatch(ServerRequestInterface $request)
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $router) {
            foreach ($this->dispatches as $attrs) {
                $router->addRoute($attrs['methods'], $attrs['path'], $attrs['handler']);
            }
        });
        $method = $request->getMethod();

        // virtual method
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['_method'])) {
            $method = strtoupper($parsedBody['_method']);
        }
        $routeInfo = $this->runDispatcher($dispatcher, $method, $request->getUri()->getPath());
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        return $this->routes[$routeInfo[1]]->execute($request);
    }

    /**
     * @param \FastRoute\Dispatcher $dispatcher
     * @param string $method
     * @param string $path
     * @return array
     */
    protected function runDispatcher(Dispatcher $dispatcher, $method, $path)
    {
        $routeInfo = $dispatcher->dispatch($method, $path);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new HandlerNotFoundException();
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException();
            case Dispatcher::FOUND:
                return $routeInfo;
        }
    }
}
