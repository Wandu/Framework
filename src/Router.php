<?php
namespace Wandu\Router;

use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Closure;
use Wandu\Router\Exception\HandlerNotFoundException;
use Wandu\Router\Exception\MethodNotAllowedException;

class Router
{
    use RouterMethodsTrait;

    /** @var Route[] */
    protected $routes = [];

    /** @var MapperInterface */
    protected $mapper;

    /** @var FastRoute */
    protected $fastRoute;

    /** @var array */
    protected $attributes;

    /**
     * @param MapperInterface $mapper
     * @param array $attributes
     */
    public function __construct(MapperInterface $mapper, array $attributes = [])
    {
        $this->mapper = $mapper;
        $this->fastRoute = new FastRoute();
        $this->attributes = $attributes + [
                'prefix' => '',
                'middleware' => [],
            ];
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
     * @return Route
     */
    public function createRoute($methods, $path, $handler, array $middlewares = [])
    {
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        $path = $this->attributes['prefix'] . $path ?: '/';
        $middlewares = array_merge($this->attributes['middleware'], $middlewares);

        $handleKey =  implode(',', $methods) . $path;
        $this->fastRoute->addRoute($methods, $path, $handleKey);
        return $this->routes[$handleKey] = new Route($handler, $middlewares);
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
        $dispatcher = $this->fastRoute->getDispatcher();
        $method = $request->getMethod();
        $parsedBody = $request->getParsedBody();
        if (isset($parsedBody['_method'])) {
            $method = strtoupper($parsedBody['_method']);
        }
        $routeInfo = $this->runDispatcher($dispatcher, $method, $request->getUri()->getPath());
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        return $this->routes[$routeInfo[1]]->execute($request, $this->mapper);
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
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new HandlerNotFoundException();
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException();
            case Dispatcher::FOUND:
                return $routeInfo;
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
