<?php
namespace Wandu\Router\Loader;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Wandu\DI\ContainerInterface;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\MiddlewareInterface;
use Wandu\Router\Exception\HandlerNotFoundException;

class WanduLoader implements LoaderInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    protected $container;

    /**
     * @param \Wandu\DI\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function middleware($className): MiddlewareInterface
    {
        try {
            return $this->container->create($className);
        } catch (Exception $e) {
            throw new HandlerNotFoundException($className);
        } catch (Throwable $e) {
            throw new HandlerNotFoundException($className);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute($className, $methodName, ServerRequestInterface $request)
    {
        try {
            $object = $this->container->get($className);
        } catch (Exception $e) {
            throw new HandlerNotFoundException($className, $methodName);
        } catch (Throwable $e) {
            throw new HandlerNotFoundException($className, $methodName);
        }
        if (!method_exists($object, $methodName) && !method_exists($object, '__call')) {
            throw new HandlerNotFoundException($className, $methodName);
        }
        return $this->container->with([
            ServerRequest::class => $request,
        ])->call([$object, $methodName]);
    }
}
