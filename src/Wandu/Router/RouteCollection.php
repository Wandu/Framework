<?php
namespace Wandu\Router;

use FastRoute\DataGenerator\GroupCountBased as GCBGenerator;
use IteratorAggregate;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\Dispatchable;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Contracts\Routable;
use Wandu\Router\Contracts\RouteFluent;
use Wandu\Router\Path\Pattern;

class RouteCollection implements Routable, IteratorAggregate, Dispatchable
{
    /** @var \Wandu\Router\RouteCollection[] */
    protected $routers = [];

    /** @var \Wandu\Router\Route[] */
    protected $routes = [];

    /** @var string */
    protected $prefix;
    
    /** @var array */
    protected $middlewares;
    
    /** @var array */
    protected $domains;

    /**
     * @param string $prefix
     * @param array $middlewares
     * @param array $domains
     */
    public function __construct($prefix = '', array $middlewares = [], array $domains = [])
    {
        $this->prefix = $prefix;
        $this->middlewares = $middlewares;
        $this->domains = $domains;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->getIterator());
    }

    /**
     * {@inheritdoc}
     */
    public function prefix(string $prefix, callable $handler)
    {
        $this->group(['prefix' => $prefix, ], $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function domain($domain, callable $handler)
    {
        $this->group(['domain' => $domain, ], $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function middleware($middleware, callable $handler)
    {
        $this->group(['middleware' => $middleware, ], $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function group(array $attributes, callable $handler)
    {
        $prefix = $this->prefix;
        if (isset($attributes['prefix'])) {
            $prefix .= "/" . $attributes['prefix'];
        }
        $middlewares = $this->middlewares;
        if (isset($attributes['middleware'])) {
            $middlewares = array_merge($middlewares, array_filter((array)$attributes['middleware']));
        }
        if (isset($attributes['middlewares'])) {
            $middlewares = array_merge($middlewares, array_filter((array)$attributes['middlewares']));
        }
        $domains = $this->domains;
        if (isset($attributes['domain'])) {
            $domains = (array) $attributes['domain'];
        }
        if (isset($attributes['domains'])) {
            $domains = (array) $attributes['domains'];
        }
        $router = new RouteCollection($prefix, $middlewares, $domains);
        call_user_func($handler, $router);
        $this->routers[] = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function append(callable $handler)
    {
        call_user_func($handler, $this);
    }

    public function resource($className, $except = [], $only = [])
    {
        $this->get('', $className, 'index');
        $this->post('', $className, 'store');
        $this->get(':id', $className, 'show');
        $this->put(':id', $className, 'update');
        $this->patch(':id', $className, 'patch');
        $this->delete(':id', $className, 'destroy');
        // $this->options();
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $path, string $className, string $methodName = 'index'): RouteFluent
    {
        return $this->createRoute(['GET', 'HEAD'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function post(string $path, string $className, string $methodName = 'index'): RouteFluent
    {
        return $this->createRoute(['POST'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $path, string $className, string $methodName = 'index'): RouteFluent
    {
        return $this->createRoute(['PUT'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $path, string $className, string $methodName = 'index'): RouteFluent
    {
        return $this->createRoute(['DELETE'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function options(string $path, string $className, string $methodName = 'index'): RouteFluent
    {
        return $this->createRoute(['OPTIONS'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function patch(string $path, string $className, string $methodName = 'index'): RouteFluent
    {
        return $this->createRoute(['PATCH'], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function any(string $path, string $className, string $methodName = 'index'): RouteFluent
    {
        return $this->createRoute([
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'OPTIONS',
            'PATCH'
        ], $path, $className, $methodName);
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute(array $methods, string $path, string $className, string $methodName = 'index'): RouteFluent
    {
        $path = trim("{$this->prefix}/{$path}", '/');
        while(strpos($path, '//') !== false) {
            $path = str_replace('//', '/', $path);
        }
        $path = '/' . $path;
        $route = new Route($className, $methodName, $this->middlewares, $this->domains);
        $this->routes[] = [$methods, $path, $route];
        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        foreach ($this->routes as $route) {
            yield $route;
        }
        foreach ($this->routers as $router) {
            foreach ($router as $route) {
                yield $route;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(LoaderInterface $loader, ResponsifierInterface $responsifier, ServerRequestInterface $request)
    {
        return $this->compile()->dispatch($loader, $responsifier, $request);
    }

    /**
     * @return \Wandu\Router\CompiledRouteCollection
     */
    public function compile(): CompiledRouteCollection
    {
        $routeMap = [];

        /** @var \FastRoute\DataGenerator\GroupCountBased[] $generators */
        $generators = [
            '@' => new GCBGenerator(),
        ];

        /**
         * @var array|string[] $methods
         * @var string $path
         * @var \Wandu\Router\Route $route
         */
        foreach ($this->getIterator() as list($methods, $path, $route)) {
            $pathPattern = new Pattern($path);

            $handleId = uniqid('HANDLER');
            $routeMap[$handleId] = $route;

            foreach ($pathPattern->parse() as $parsedPath) {
                foreach ($methods as $method) {
                    $domains = $route->getDomains();
                    if (count($domains)) {
                        foreach ($domains as $domain) {
                            if (!isset($generators[$domain])) {
                                $generators[$domain] = new GCBGenerator();
                            }
                            $generators[$domain]->addRoute($method, $parsedPath, $handleId);
                        }
                    } else {
                        $generators['@']->addRoute($method, $parsedPath, $handleId);
                    }
                }
            }
        }

        $compiledRoutes = array_map(function (GCBGenerator $generator) {
            return $generator->getData();
        }, $generators);
        return new CompiledRouteCollection($compiledRoutes, $routeMap);
    }
}
