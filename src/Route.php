<?php
namespace June;

use June\Request\Pattern;
use Mockery\Matcher\Closure;
use Phly\Http\Request;
use Psr\Http\Message\RequestInterface;

use InvalidArgumentException;

class Route
{
    /** @var string */
    protected $method;

    /** @var string */
    protected $path;

    /** @var callable */
    protected $handler;

    /** @var array */
    protected $args = array();

    /** @var string */
    protected $pattern = null;

    /** @var Pattern */
    protected $patternParser;

    /** @var array */
    protected $middleware;

    /** @var int */
    protected $nextCount = 0;

    /**
     * @param $method
     * @param $path
     * @param callable $handler
     * @param array $middleware
     */
    public function __construct($method, $path, callable $handler, array $middleware = [])
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;

        $this->middleware = $middleware;

        $this->patternParser = new Pattern($this->path);
    }

    /**
     * @param string $method
     * @param string $path
     * @return bool
     */
    public function isExecutable($method, $path)
    {
        if (strtolower($method) === strtolower($this->getMethod()) &&
            strtolower($path) === strtolower($this->getPath())) {
            return true;
        }
        return false;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function execute(RequestInterface $request)
    {
        $this->nextCount = 0;
        if (count($this->middleware) > 0) {
            return call_user_func($this->middleware[$this->nextCount++], $request, function (RequestInterface $request) {
                return $this->next($request);
            });
        } else {
            return call_user_func($this->handler, $request);
        }
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function next(RequestInterface $request)
    {
        if (count($this->middleware) <= $this->nextCount) {
            return call_user_func($this->handler, $request);
        } else {
            return call_user_func($this->middleware[$this->nextCount++], $request, function (RequestInterface $request) {
                return $this->next($request);
            });
        }
    }

    /**
     * @return array $middleware
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @param callable|array $middleware
     */
    public function setMiddleware($middleware)
    {
        if (is_callable($middleware)) {
            $this->middleware[] = $middleware;
        } else if (is_array($middleware)) {
            foreach ($middleware as $callable) {
                if (!is_callable($callable)) {
                    throw new InvalidArgumentException("middleware not to be callable");
                }
            }
            $this->middleware = array_merge($this->middleware, $middleware);
        } else {
            throw new InvalidArgumentException("middleware not to be callable");
        }
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return callable
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        if (!isset($this->args) || empty($this->args)) {
            $this->args = $this->patternParser->getArgs();
        }
        return $this->args;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        if (!isset($this->pattern)) {
            $this->pattern = $this->patternParser->getPattern();
        }
        return $this->pattern;
    }
}
