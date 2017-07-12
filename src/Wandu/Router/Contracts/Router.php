<?php
namespace Wandu\Router\Contracts;

interface Router
{
    /**
     * @param array|string $domain
     * @param callable $handler
     */
    public function domain($domain, callable $handler);
    
    /**
     * @param string $prefix
     * @param callable $handler
     */
    public function prefix(string $prefix, callable $handler);

    /**
     * @param array|string $middleware
     * @param callable $handler
     */
    public function middleware($middleware, callable $handler);

    /**
     * @param array $attributes
     * @param callable $handler
     */
    public function group(array $attributes, callable $handler);

    /**
     * @param callable $handler
     */
    public function append(callable $handler);

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function get(string $path, string $className, string $methodName = 'index'): RouteFluent;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function post(string $path, string $className, string $methodName = 'index'): RouteFluent;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function put(string $path, string $className, string $methodName = 'index'): RouteFluent;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function delete(string $path, string $className, string $methodName = 'index'): RouteFluent;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function options(string $path, string $className, string $methodName = 'index'): RouteFluent;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function patch(string $path, string $className, string $methodName = 'index'): RouteFluent;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function any(string $path, string $className, string $methodName = 'index'): RouteFluent;

    /**
     * @param array $methods
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function createRoute(array $methods, string $path, string $className, string $methodName = 'index'): RouteFluent;
}
