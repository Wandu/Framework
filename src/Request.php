<?php
namespace June;

use June\Request\Pattern;

class Request
{
    protected $data;

    protected $pattern;

    public function __construct(Array $attribute)
    {
        $this->data = $attribute;
        $this->pattern = new Pattern($this->data['path']);
    }

    public function getPath()
    {
        return $this->data['path'];
    }

    public function getUri()
    {
        if (!isset($this->data['uri'])) {
            $this->data['uri'] = $this->pattern->parseUri();
        }
        return $this->data['uri'];
    }

    public function getArgs()
    {
        if (!isset($this->data['args'])) {
            $this->data['args'] = $this->pattern->getArgs();
        }
        return $this->data['args'];
    }

    public function getPattern()
    {
        if (!isset($this->data['pattern'])) {
            $this->data['pattern'] = $this->pattern->getPattern();
        }
        return $this->data['pattern'];
    }

    public function getMethod()
    {
        if (isset($this->data['method'])) {
            return $this->data['method'];
        }
    }

    // getBody, getHeaders, getQueries, getParameters, getHeader, getQuery, getParameter
}
