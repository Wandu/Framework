<?php
namespace June;

use June\Request\Pattern;
use Phly\Http\Request;
use Psr\Http\Message\RequestInterface;

class Route
{
    protected $method;

    protected $path;

    protected $handler;

    protected $args;

    protected $pattern;

    protected $patternParser;

    public function __construct($method, $path, callable $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->patternParser = new Pattern($this->path);
        $this->handler = $handler;
    }

    public function isExecutable($method, $path)
    {
        if (strtolower($method) === strtolower($this->getMethod()) &&
            strtolower($path) === strtolower($this->getPath())) {
            return true;
        }
        return false;
    }

    public function execute(RequestInterface $request)
    {
        return call_user_func($this->getHandler(), $request);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getArgs()
    {
        if (!isset($this->args)) {
            $this->args = $this->patternParser->getArgs();
        }
        return $this->args;
    }

    public function getPattern()
    {
        if (!isset($this->pattern)) {
            $this->pattern = $this->patternParser->getPattern();
        }
        return $this->pattern;
    }

    // getBody, getHeaders, getQueries, getParameters, getHeader, getQuery, getParameter
}
