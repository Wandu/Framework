<?php
namespace Wandu\Router;

use Wandu\Router\Contracts\RouteFluent;
use Wandu\Router\Contracts\RouteInformation;

class Route implements RouteFluent, RouteInformation
{
    /** @var string */
    protected $className;

    /** @var string */
    protected $methodName;

    /** @var array */
    protected $middlewares;

    /** @var array */
    protected $domains;

    /**
     * @param string $className
     * @param string $methodName
     * @param string|array $middlewares
     * @param string|array $domains
     */
    public function __construct($className, $methodName, $middlewares = [], $domains = [])
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
        $this->domains = is_array($domains) ? $domains : [$domains];
    }

    /**
     * {@inheritdoc}
     */
    public function middleware($middlewares, $overwrite = false): RouteFluent
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }
        $this->middlewares = $overwrite
            ? $middlewares
            : array_merge($this->middlewares, $middlewares);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function domains($domains = []): RouteFluent
    {
        if (is_string($domains)) {
            $domains = [$domains];
        }
        $this->domains = $domains;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomains(): array
    {
        return $this->domains;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
