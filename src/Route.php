<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;

class Route
{
    /** @var string */
    protected $className;

    /** @var string */
    protected $methodName;

    /** @var array */
    protected $middlewares;

    /**
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     */
    public function __construct($className, $methodName, array $middlewares = [])
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->middlewares = $middlewares;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     */
    public function execute(ServerRequestInterface $request)
    {
        return $this->dispatch($request, $this->middlewares);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     */
    public function dispatch(ServerRequestInterface $request, array $middlewares)
    {
        if (count($middlewares)) {
            $middleware = array_shift($middlewares);
            return call_user_func([new $middleware, 'handle'], $request, function () use ($request, $middlewares) {
                return $this->dispatch($request, $middlewares);
            });
        }
        return call_user_func([new $this->className, $this->methodName], $request);
    }
}
