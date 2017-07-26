<?php
namespace Wandu\Router\Contracts;

interface RouteFluent
{
    /**
     * @param string|array $middlewares
     * @param bool $overwrite
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function middleware($middlewares, $overwrite = false): RouteFluent;

    /**
     * @param string|array $domains
     * @return \Wandu\Router\Contracts\RouteFluent
     */
    public function domains($domains = []): RouteFluent;
}
