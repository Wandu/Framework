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
        $routeMap = [];
        $router = new Router;
        $router->middleware($config->getMiddleware(), $handler);

        /** @var \FastRoute\DataGenerator\GroupCountBased[] $generators */
        $generators = [];
        /**
         * @var array|string[] $methods
         * @var string $path
         * @var \Wandu\Router\Route $route
         */
        foreach ($router as list($methods, $path, $route)) {
            $pathPattern = new Pattern($path);

            $handleId = uniqid('HANDLER');
            $routeMap[$handleId] = $route;

            foreach ($pathPattern->parse() as $parsedPath) {
                foreach ($methods as $method) {
                    foreach ($route->getDomains() as $domain) {
                        if (!isset($generators[$domain])) {
                            $generators[$domain] = new GCBGenerator();
                        }
                        $generators[$domain]->addRoute($method, $parsedPath, $handleId);
                    }
                }
            }
        }

        $compiledRoutes = array_map(function (GCBGenerator $generator) {
            return $generator->getData();
        }, $generators);
        return new static($compiledRoutes, $routeMap);
    }

    /** @var \Wandu\Router\Route[] */
    protected $routeMap = [];

    /** @var array */
    protected $compiledRoutes = [];

    /**
     * @param array $compiledRoutes
     * @param array $routeMap
     */
    public function __construct(array $compiledRoutes, array $routeMap)
    {
        $this->routeMap = $routeMap;
        $this->compiledRoutes = $compiledRoutes;
    }
    
    public function dispatch(ServerRequestInterface $request, LoaderInterface $loader = null, ResponsifierInterface $responsifier = null)
    {
        $host = $request->getHeaderLine('host');
        $compiledRoutes = array_key_exists($host, $this->compiledRoutes)
            ? $this->compiledRoutes[$host]
            : $this->compiledRoutes['@'];
        
        $routeInfo = (new GCBDispatcher($compiledRoutes))
            ->dispatch($request->getMethod(), '/' . trim($request->getUri()->getPath(), '/'));
        
        switch ($routeInfo[0]) {
            case FastDispatcher::NOT_FOUND:
                throw new RouteNotFoundException();
            case FastDispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException();
        }
        $route = $this->routeMap[$routeInfo[1]];
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        
        return $route->execute($request, $loader, $responsifier);
    }
}
