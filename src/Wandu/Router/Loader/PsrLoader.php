<?php
namespace Wandu\Router\Loader;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use RuntimeException;
use ReflectionException;
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
                return call_user_func_array([$object, $methodName], $arguments);
            }
            if (method_exists($object, '__call')) {
                return $object->__call($methodName, [$request]);
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
            $classRefl = new ReflectionClass($className);
            $constructor = $classRefl->getConstructor();
            try {
                if ($constructor) {
                    $arguments = $this->getArguments($constructor, $request);
                    return new $className(...$arguments);
                } else {
                    return new $className();
                }
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
    protected function getArguments(\ReflectionFunctionAbstract $refl, ServerRequestInterface $request)
    {
        $requestAttrs = $request->getAttributes();
        $arguments = [];
        // it from container Resolver..
        foreach ($refl->getParameters() as $param) {
            $paramClass = $param->getClass();
            if ($paramClass) { // #2.
                if ($paramClass->isInstance($request)) {
                    $arguments[] = $request;
                    continue;
                }
                $paramClassName = $paramClass->getName();
                if (array_key_exists($paramClassName, $requestAttrs)) {
                    $arguments[] = $requestAttrs[$paramClassName];
                    continue;
                }
                if ($this->container->has($paramClassName)) {
                    try {
                        $arguments[] = $this->container->get($paramClassName);
                        continue;
                    } catch (NotFoundExceptionInterface $e) {
                    }
                }
            }
            if ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
                continue;
            }
            $paramName = $param->getName();
            if (array_key_exists($paramName, $requestAttrs)) {
                $arguments[] = $requestAttrs[$paramName];
                continue;
            }
            throw new RuntimeException("not found parameter named \"{$param->getName()}\".");
        }
        return $arguments;
    }
}
