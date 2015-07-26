<?php
namespace Wandu\Router;

use ArrayAccess;
use Psr\Http\Message\ServerRequestInterface;

class Route
{
    /** @var string|callable */
    protected $handler;

    /** @var array */
    protected $middlewares;

    /** @var int */
    protected $nextCount;

    /** @var HandlerMapperInterface */
    protected $handlerMapper;

    /**
     * @param string|callable $handler
     * @param array $middlewares
     */
    public function __construct($handler, array $middlewares = [])
    {
        $this->handler = $handler;
        $this->middlewares = $middlewares;
    }

    /**
     * @param ServerRequestInterface $request
     * @param HandlerMapperInterface $handlerMapper
     * @return mixed
     */
    public function execute(ServerRequestInterface $request, HandlerMapperInterface $handlerMapper = null)
    {
        $this->nextCount = 0;
        $this->handlerMapper = $handlerMapper;
        return $this->next($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function next(ServerRequestInterface $request)
    {
        if (!isset($this->middlewares[$this->nextCount])) {
            return call_user_func($this->filterHandler($this->handler), $request);
        }
        $handler = $this->filterMiddleware($this->middlewares[$this->nextCount]);
        return call_user_func($handler, $request, function (ServerRequestInterface $request) {
            $this->nextCount++;
            return $this->next($request);
        });
    }

    /**
     * @param string|callable $handler
     * @return callable
     */
    protected function filterHandler($handler)
    {
        if (!is_callable($handler)) {
            $handler = $this->handlerMapper->mapHandler($handler);
        }
        return $handler;
    }

    /**
     * @param string|callable $handler
     * @return callable
     */
    protected function filterMiddleware($handler)
    {
        if (!is_callable($handler)) {
            $handler = $this->handlerMapper->mapMiddleware($handler);
        }
        return $handler;
    }
}
