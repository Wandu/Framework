<?php
namespace Wandu\Router\Contracts;

use Wandu\Router\Route;

interface Router
{
    /**
     * @param string $prefix
     * @param callable $handler
     */
    public function prefix(string $prefix, callable $handler);

    /**
     * @param array|string $middlewares
     * @param callable $handler
     */
    public function middleware($middlewares, callable $handler);

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
     * @return \Wandu\Router\Route
     */
    public function get(string $path, string $className, string $methodName = 'index'): Route;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Route
     */
    public function post(string $path, string $className, string $methodName = 'index'): Route;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Route
     */
    public function put(string $path, string $className, string $methodName = 'index'): Route;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Route
     */
    public function delete(string $path, string $className, string $methodName = 'index'): Route;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Route
     */
    public function options(string $path, string $className, string $methodName = 'index'): Route;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Route
     */
    public function patch(string $path, string $className, string $methodName = 'index'): Route;

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Route
     */
    public function any(string $path, string $className, string $methodName = 'index'): Route;

    /**
     * @param array $methods
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @return \Wandu\Router\Route
     */
    public function createRoute(array $methods, string $path, string $className, string $methodName = 'index'): Route;
}
