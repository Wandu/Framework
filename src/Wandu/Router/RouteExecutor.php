<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\ClassLoader\DefaultLoader;
use Wandu\Router\Contracts\ClassLoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Responsifier\NullResponsifier;
use function Wandu\Http\response;

class RouteExecutor
{
    /**
     * @param \Wandu\Router\Contracts\ClassLoaderInterface $loader
     * @param \Wandu\Router\Contracts\ResponsifierInterface $responsifier
     */
    public function __construct(
        ClassLoaderInterface $loader = null,
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
            /** @var \Wandu\Router\Contracts\MiddlewareInterface $middleware */
            $middleware = $this->loader->create(array_shift($middlewares));
            $response = call_user_func(
                $middleware,
                $request,
                function (ServerRequestInterface $request) use ($className, $methodName, $middlewares) {
                    return $this->execute($request, $className, $methodName, $middlewares);
                }
            );
            return $this->responsifier->responsify($response);
        }
        $controllerClass = $this->loader->create($className);
        return $this->responsifier->responsify(
            $this->loader->call($request, $controllerClass, $methodName)
        );
    }
}
