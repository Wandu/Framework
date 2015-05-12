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

    /**
     * @param array $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function execute(RequestInterface $request, ArrayAccess $controllers)
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
        $condition = is_null($condition) ? count($this->handlers) > $this->nextCount : $condition;
        if ($condition) {
            // callable ? none callable? 
            return call_user_func($this->handlers[$this->nextCount++], $request, function (RequestInterface $request) {
                return $this->next($request);
            });
        }
    }
}
