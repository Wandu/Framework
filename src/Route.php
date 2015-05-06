<?php
namespace June;

use June\Request\Pattern;
use Phly\Http\Request;
use Psr\Http\Message\RequestInterface;

class Route
{
    /** @var string */
    protected $method;

    /** @var string */
    protected $path;

    /** @var callable */
    protected $handler;

    /** @var array */
    protected $args;

    /** @var string */
    protected $pattern;

    /** @var Pattern */
    protected $patternParser;

    /**
     * @param string $method
     * @param string $path
     * @param callable $handler
     */
    public function __construct($method, $path, callable $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->patternParser = new Pattern($this->path);
        $this->handler = $handler;
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
        return call_user_func($this->getHandler(), $request);
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
        if (!isset($this->args)) {
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
