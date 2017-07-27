<?php
namespace Wandu\Router;

use FastRoute\DataGenerator\GroupCountBased as GCBGenerator;
use FastRoute\Dispatcher as FastDispatcher;
use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\Dispatchable;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Contracts\Routable;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Path\Pattern;

class CompiledRoutes implements Dispatchable
{
    /**
     * @param \Wandu\Router\Contracts\Routable $router
     * @return \Wandu\Router\CompiledRoutes
     */
    public static function compile(Routable $router)
    {
        $routeMap = [];

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
                    $domains = $route->getDomains();
                    if (count($domains)) {
                        foreach ($domains as $domain) {
                            if (!isset($generators[$domain])) {
                                $generators[$domain] = new GCBGenerator();
                            }
                            $generators[$domain]->addRoute($method, $parsedPath, $handleId);
                        }
                    } else {
                        if (!isset($generators['@'])) {
                            $generators['@'] = new GCBGenerator();
                        }
                        $generators['@']->addRoute($method, $parsedPath, $handleId);
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
    
    public function dispatch(LoaderInterface $loader, ResponsifierInterface $responsifier, ServerRequestInterface $request)
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
        $executor = new RouteExecutor($loader, $responsifier);
        return $executor->execute($route, $request);
    }
}
