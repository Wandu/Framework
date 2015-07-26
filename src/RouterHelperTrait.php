<?php
namespace Wandu\Router;

trait RouterHelperTrait
{
    /**
     * @param string $path
     * @param array|string|callable $middlewares
     * @param string|callable $handler
     */
    public function get($path, $middlewares, $handler = null)
    {
        $this->normalizedRoute(['GET'], $path, $middlewares, $handler);
    }

    /**
     * @param string $path
     * @param array|string|callable $middlewares
     * @param string|callable $handler
     */
    public function post($path, $middlewares, $handler = null)
    {
        $this->normalizedRoute(['POST'], $path, $middlewares, $handler);
    }

    /**
     * @param string $path
     * @param array|string|callable $middlewares
     * @param string|callable $handler
     */
    public function put($path, $middlewares, $handler = null)
    {
        $this->normalizedRoute(['PUT'], $path, $middlewares, $handler);
    }

    /**
     * @param string $path
     * @param array|string|callable $middlewares
     * @param string|callable $handler
     */
    public function delete($path, $middlewares, $handler = null)
    {
        $this->normalizedRoute(['DELETE'], $path, $middlewares, $handler);
    }

    /**
     * @param string $path
     * @param array|string|callable $middlewares
     * @param string|callable $handler
     */
    public function options($path, $middlewares, $handler = null)
    {
        $this->normalizedRoute(['OPTIONS'], $path, $middlewares, $handler);
    }

    /**
     * @param string $path
     * @param array|string|callable $middlewares
     * @param string|callable $handler
     */
    public function any($path, $middlewares, $handler = null)
    {
        $this->normalizedRoute(['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], $path, $middlewares, $handler);
    }

    protected function normalizedRoute($methods, $path, $middlewares, $handler = null)
    {
        if (isset($handler)) {
            $this->createRoute($methods, $path, $handler, $middlewares);
            return;
        }
        $this->createRoute($methods, $path, $middlewares);
    }
}
