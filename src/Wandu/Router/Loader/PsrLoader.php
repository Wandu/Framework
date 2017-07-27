<?php
namespace Wandu\Router\Loader;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use RuntimeException;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class PsrLoader implements LoaderInterface
{
    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    /**
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function middleware(string $className, ServerRequestInterface $request): MiddlewareInterface
    {
        /** @var \Wandu\Router\Contracts\MiddlewareInterface $instance */
        $instance = $this->createInstance($className, $request);
        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $className, string $methodName, ServerRequestInterface $request)
    {
        try {
            $object = $this->createInstance($className, $request);
            try {
                $reflMethod = (new ReflectionClass($className))->getMethod($methodName);
            } catch (ReflectionException $e) {
                $reflMethod = null;
            }
            if ($reflMethod) {
                $arguments = $this->getArguments($reflMethod, $request);
                return $object->{$methodName}(...$arguments);
            }
            if (method_exists($object, '__call')) {
                return $object->{$methodName}($request);
            }
        } catch (HandlerNotFoundException $e) {
            throw new HandlerNotFoundException($className, $methodName);
        }
        throw new HandlerNotFoundException($className, $methodName);
    }

    /**
     * @param string $className
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return object
     */
    public function createInstance(string $className, ServerRequestInterface $request)
    {
        if (class_exists($className)) {
            try {
                $arguments = $this->getArguments((new ReflectionClass($className))->getConstructor(), $request);
                return new $className(...$arguments);
            } catch (RuntimeException $e) {}
        } elseif ($this->container->has($className)) { // for alias
            return $this->container->get($className);
        }
        throw new HandlerNotFoundException($className);
    }

    /**
     * @param \ReflectionFunctionAbstract $refl
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    protected function getArguments(ReflectionFunctionAbstract $refl = null, ServerRequestInterface $request)
    {
        if (!$refl) {
            return [];
        }
        $attributes = $request->getAttributes();
        $arguments = [];
        foreach ($refl->getParameters() as $param) {
            $arguments[] = $this->getArgument($param, $request, $attributes);
        }
        return $arguments;
    }

    /**
     * @param \ReflectionParameter $param
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array $attributes
     * @return mixed
     */
    protected function getArgument(ReflectionParameter $param, ServerRequestInterface $request, array $attributes = [])
    {
        $paramClassRefl = $param->getClass();
        if ($paramClassRefl) { // #2.
            if ($paramClassRefl->isInstance($request)) {
                return $request;
            }
            $paramClassName = $paramClassRefl->getName();
            if (array_key_exists($paramClassName, $attributes)) {
                return $attributes[$paramClassName];
            }
            if ($this->container->has($paramClassName)) {
                try {
                    return $this->container->get($paramClassName);
                } catch (NotFoundExceptionInterface $e) {
                }
            }
        }
        $paramName = $param->getName();
        if (array_key_exists($paramName, $attributes)) {
            return $attributes[$paramName];
        }
        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }
        throw new RuntimeException("not found parameter named \"{$param->getName()}\".");
    }
}
