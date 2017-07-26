<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Contracts\RouteInformation;

class RouteExecutor
{
    /**
     * @param \Wandu\Router\Contracts\LoaderInterface $loader
     * @param \Wandu\Router\Contracts\ResponsifierInterface $responsifier
     */
    public function __construct(LoaderInterface $loader, ResponsifierInterface $responsifier)
    {
        $this->loader = $loader;
        $this->responsifier = $responsifier;
    }

    /**
     * @param \Wandu\Router\Contracts\RouteInformation $route
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function execute(RouteInformation $route, ServerRequestInterface $request)
    {
        return $this->next($request, $route->getClassName(), $route->getMethodName(), $route->getMiddlewares());
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function next(ServerRequestInterface $request, $className, $methodName, array $middlewares = [])
    {
        if (count($middlewares)) {
            $middleware = $this->loader->middleware(array_shift($middlewares), $request);
            $response = $middleware->__invoke($request, function (ServerRequestInterface $request) use ($className, $methodName, $middlewares) {
                return $this->next($request, $className, $methodName, $middlewares);
            });
            return $this->responsifier->responsify($response);
        }
        return $this->responsifier->responsify(
            $this->loader->execute($className, $methodName, $request)
        );
    }
}
