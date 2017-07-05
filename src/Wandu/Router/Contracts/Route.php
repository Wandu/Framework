<?php
namespace Wandu\Router\Contracts;

interface Route
{
    /**
     * @param string|array $middlewares
     * @param bool $overwrite
     * @return \Wandu\Router\Contracts\Route
     */
    public function middleware($middlewares, $overwrite = false): Route;

    /**
     * @param string|array $domains
     * @return \Wandu\Router\Contracts\Route
     */
    public function domains($domains): Route;
}
