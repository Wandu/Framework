<?php
namespace Wandu\Router;

trait ShortRouterMethods
{
    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function get($path, $className, $methodName, array $middlewares = [])
    {
        return $this->createRoute(['GET', 'HEAD'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function post($path, $className, $methodName, array $middlewares = [])
    {
        return $this->createRoute(['POST'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function put($path, $className, $methodName, array $middlewares = [])
    {
        return $this->createRoute(['PUT'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function delete($path, $className, $methodName, array $middlewares = [])
    {
        return $this->createRoute(['DELETE'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function options($path, $className, $methodName, array $middlewares = [])
    {
        return $this->createRoute(['OPTIONS'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function patch($path, $className, $methodName, array $middlewares = [])
    {
        return $this->createRoute(['PATCH'], $path, $className, $methodName, $middlewares);
    }

    /**
     * @param string $path
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @return Route
     */
    public function any($path, $className, $methodName, array $middlewares = [])
    {
        return $this->createRoute([
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'OPTIONS',
            'PATCH'
        ], $path, $className, $methodName, $middlewares);
    }
}
