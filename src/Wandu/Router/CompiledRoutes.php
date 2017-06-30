<?php
namespace Wandu\Router;

use Closure;
use FastRoute\DataGenerator\GroupCountBased as GCBGenerator;
use FastRoute\Dispatcher as FastDispatcher;
use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Path\Pattern;

class CompiledRoutes
{
    /**
     * @param \Closure $handler
     * @param \Wandu\Router\Configuration $config
     * @return \Wandu\Router\CompiledRoutes
     */
    public static function compile(Closure $handler, Configuration $config)
    {
        $resultRoutes = [];
        $resultNamedPath = [];

        $router = new Router;
        $router->middleware($config->getMiddleware(), $handler);

        $generator = new GCBGenerator();
        /**
         * @var array|string[] $methods
         * @var string $path
         * @var \Wandu\Router\Route $route
         */
        foreach ($router as list($methods, $path, $route)) {
            $pathPattern = new Pattern($path);
            if ($routeName = $route->getName()) {
                $resultNamedPath[$routeName] = $pathPattern;
            }
            foreach ($pathPattern->parse() as $parsedPath) {
                foreach ($methods as $method) {
                    $handleId = uniqid('HANDLER');
                    $generator->addRoute($method, $parsedPath, $handleId);
                    $resultRoutes[$handleId] = $route;
                }
            }
        }
        return new static($resultRoutes, $resultNamedPath, $generator->getData());
    }

    /** @var \Wandu\Router\Route[] */
    protected $routes = [];

    /** @var \Wandu\Router\Route[] */
    protected $namedPattern;

    /** @var array */
    protected $compiledRoutes = [];

    /**
     * @param array $routes
     * @param array $namedPattern
     * @param array $compiledRoutes
     */
    public function __construct(array $routes, array $namedPattern, array $compiledRoutes)
    {
        $this->routes = $routes;
        $this->namedPattern = $namedPattern;
        $this->compiledRoutes = $compiledRoutes;
    }
    
    public function getPattern($name): Pattern
    {
        if (!isset($this->namedPattern[$name])) {
            throw new RouteNotFoundException("Route \"{$name}\" not found.");
        }
        return $this->namedPattern[$name];

    }
    public function dispatch(ServerRequestInterface $request, LoaderInterface $loader = null, ResponsifierInterface $responsifier = null)
    {
        $routeInfo = (new GCBDispatcher($this->compiledRoutes))
            ->dispatch($request->getMethod(), '/' . trim($request->getUri()->getPath(), '/'));
        
        switch ($routeInfo[0]) {
            case FastDispatcher::NOT_FOUND:
                throw new RouteNotFoundException();
            case FastDispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException();
        }
        $route = $this->routes[$routeInfo[1]];
        if (count($domains = $route->getDomains())) {
            if (!in_array($request->getHeaderLine('host'), $domains)) {
                throw new RouteNotFoundException();
            }
        }
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        
        return $route->execute($request, $loader, $responsifier);
    }
}
