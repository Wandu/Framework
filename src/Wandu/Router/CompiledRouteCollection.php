<?php
namespace Wandu\Router;

use FastRoute\Dispatcher as FastDispatcher;
use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\Dispatchable;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Exception\MethodNotAllowedException;
use Wandu\Router\Exception\RouteNotFoundException;

class CompiledRouteCollection implements Dispatchable
{
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

    /**
     * {@inheritdoc}
     */
    public function dispatch(
        LoaderInterface $loader,
        ResponsifierInterface $responsifier,
        ServerRequestInterface $request
    ) {
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
