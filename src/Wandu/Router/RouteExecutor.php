<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Loader\DefaultLoader;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Responsifier\NullResponsifier;

class RouteExecutor
{
    /**
     * @param \Wandu\Router\Contracts\LoaderInterface $loader
     * @param \Wandu\Router\Contracts\ResponsifierInterface $responsifier
     */
    public function __construct(
        LoaderInterface $loader = null,
        ResponsifierInterface $responsifier = null
    ) {
        $this->loader = $loader ?: new DefaultLoader();
        $this->responsifier = $responsifier ?: new NullResponsifier();
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function execute(ServerRequestInterface $request, $className, $methodName, array $middlewares = [])
    {
        if (count($middlewares)) {
            $middleware = $this->loader->middleware(array_shift($middlewares), $request);
            $response = $middleware->__invoke($request, function (ServerRequestInterface $request) use ($className, $methodName, $middlewares) {
                return $this->execute($request, $className, $methodName, $middlewares);
            });
            return $this->responsifier->responsify($response);
        }
        return $this->responsifier->responsify(
            $this->loader->execute($className, $methodName, $request)
        );
    }
}
