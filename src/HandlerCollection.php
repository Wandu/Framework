<?php
namespace June;

use ArrayAccess;
use Psr\Http\Message\RequestInterface;

class HandlerCollection
{
    /** @var array */
    protected $handlers;

    /** @var int */
    protected $nextCount = 0;

    /** @var ArrayObject */
    protected $controllers;

    /**
     * @param array $handlers
     */
    public function __construct(ArrayAccess $controllers, array $handlers = [])
    {
        $this->controllers = $controllers;
        $this->handlers = $handlers;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function execute(RequestInterface $request)
    {
        $this->nextCount = 0;
        return $this->next($request, count($this->handlers));
    }

    /**
     * @param RequestInterface $request
     * @param mixed $condition
     * @return mixed
     */
    public function next(RequestInterface $request, $condition = null)
    {
        $this->handlers[$this->nextCount] = $this->stringToCallable($this->handlers[$this->nextCount]);
        $handler = $this->handlers[$this->nextCount];
        $condition = $condition ? $condition : count($this->handlers) > $this->nextCount;

        if ($condition) {
            return call_user_func($handler, $request, function (RequestInterface $request) {
                $this->nextCount++;
                return $this->next($request);
            });
        }
    }

    public function stringToCallable($handler)
    {
        if (!is_callable($handler)) {
            $handler = [$this->controllers[$handler[0]], $handler[1]];
        }
        return $handler;
    }
}
