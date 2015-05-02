<?php
namespace June\Request;

class Pattern
{
    protected $path;
    protected $args;
    protected $pattern;
    protected $uri;

    public function __construct($path)
    {
        $this->path = $path;
        $this->args = array();
        $this->pattern = null;
        $this->uri = null;
    }

    public function parseUri()
    {
        $uri = null;

        if (substr($this->path, -1) === '/') {
            if (substr($this->path, 0, 1) === '/') {
                $uri = substr($this->path, 1, -1);
            } else {
                $uri = substr($this->path, 0, -1);
            }
        } else {
            if (substr($this->path, 0, 1) === '/') {
                $uri = substr($this->path, 1);
            } else {
                $uri = substr($this->path, 0);
            }
        }

        if (strpos($uri, ';') !== false) {
            $uri = strstr($uri, ';', true);
        } else if (strpos($uri, '?') !== false) {
            $uri = strstr($uri, '?', true);
        }

        $this->uri = $uri;
        return $this->uri;
    }

    public function parseArgsWithPattern()
    {
        $args = array();
        $pattern = null;

        if (!isset($this->uri)) {
            $this->parseUri();
        }

        $pattern = $this->uri;
        $pattern = trim($pattern, '/');
        $pattern = str_replace('/', '\\/', $pattern);

        $pattern = preg_replace_callback('#{([a-zA-Z0-9_]+)}#', function ($matches) use (&$args) {
//            print_r($matches);
            $args[] = $matches[1];
            return "(?<{$matches[1]}>[^\\/\\#]+)";
        }, $pattern);
        $this->args = $args;
        $this->pattern = $pattern;
    }

    public function getArgs()
    {
        if (!isset($this->args) || empty($this->args)) {
            $this->parseArgsWithPattern();
        }
        return $this->args;
    }

    public function getPattern()
    {
        if (!isset($this->pattern) || is_null($this->pattern)) {
            $this->parseArgsWithPattern();
        }
        return $this->pattern;
    }
}
