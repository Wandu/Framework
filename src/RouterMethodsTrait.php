<?php
namespace Wandu\Router;

trait RouterMethodsTrait
{
    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $middlewares
     * @return Route
     */
    public function get($path, $handler, array $middlewares = [])
    {
        return $this->createRoute(['GET', 'HEAD'], $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $middlewares
     * @return Route
     */
    public function post($path, $handler, array $middlewares = [])
    {
        return $this->createRoute(['POST'], $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $middlewares
     * @return Route
     */
    public function put($path, $handler, array $middlewares = [])
    {
        return $this->createRoute(['PUT'], $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $middlewares
     * @return Route
     */
    public function delete($path, $handler, array $middlewares = [])
    {
        return $this->createRoute(['DELETE'], $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $middlewares
     * @return Route
     */
    public function options($path, $handler, array $middlewares = [])
    {
        return $this->createRoute(['OPTIONS'], $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $middlewares
     * @return Route
     */
    public function patch($path, $handler, array $middlewares = [])
    {
        return $this->createRoute(['PATCH'], $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $middlewares
     * @return Route
     */
    public function any($path, $handler, array $middlewares = [])
    {
        return $this->createRoute([
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'OPTIONS',
            'PATCH'
        ], $path, $handler, $middlewares);
    }
}
