<?php
namespace Jicjjang\June;

use ArrayAccess;
use Psr\Http\Message\ServerRequestInterface;

class HandlerCollection
{
    /** @var array */
    protected $handlers;

    /** @var int */
    protected $nextCount = 0;

    /** @var ArrayAccess */
    protected $controllers;

    /**
     * @param ArrayAccess $controllers
     * @param array $handlers
     */
    public function __construct(ArrayAccess $controllers, array $handlers = [])
    {
        $this->controllers = $controllers;
        $this->handlers = $handlers;
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function execute(ServerRequestInterface $request)
    {
        $this->nextCount = 0;
        return $this->next($request, count($this->handlers));
    }

    /**
     * @param ServerRequestInterface $request
     * @param mixed $condition
     * @return mixed
     */
    public function next(ServerRequestInterface $request, $condition = null)
    {
        $this->handlers[$this->nextCount] = $this->stringToCallable($this->handlers[$this->nextCount]);
        $handler = $this->handlers[$this->nextCount];
        $condition = $condition ? $condition : count($this->handlers) > $this->nextCount;

        if ($condition) {
            return call_user_func($handler, $request, function (ServerRequestInterface $request) {
                $this->nextCount++;
                return $this->next($request);
            });
        }
    }

    public function stringToCallable($handler)
    {
        if (!is_callable($handler)) {
            list($methodName, $className) = explode('@', $handler);
            $handler = [$this->controllers[$className], $methodName];
        }
        return $handler;
    }
}
