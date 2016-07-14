<?php
namespace Wandu\Foundation\Kernels;

use ErrorException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Wandu\Config\Config;
use Wandu\Config\Contracts\ConfigInterface;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\Contracts\HttpErrorHandlerInterface;
use Wandu\Foundation\Contracts\KernelInterface;
use Wandu\Http\Exception\MethodNotAllowedException;
use Wandu\Http\Exception\NotFoundException;
use Wandu\Http\Psr\Factory\ServerRequestFactory;
use Wandu\Http\Psr\Sender\ResponseSender;
use Wandu\Router\Dispatcher;
use Wandu\Router\Exception\MethodNotAllowedException as RouteMethodException;
use Wandu\Router\Exception\RouteNotFoundException;
use Wandu\Router\Router;

class HttpRouterKernel implements KernelInterface
{
    /** @var \Wandu\DI\ContainerInterface */
    protected $app;
    
    /** @var \Wandu\Foundation\Contracts\DefinitionInterface */
    protected $definition;

    /**
     * @param \Wandu\Foundation\Contracts\DefinitionInterface $definition
     */
    public function __construct(DefinitionInterface $definition)
    {
        $this->definition = $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        $app->instance(Config::class, new Config($this->definition->configs()));
        $app->alias(ConfigInterface::class, Config::class);
        $app->alias('config', Config::class);
        $this->definition->providers($app);
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContainerInterface $app)
    {
        /* @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->request = $app->get(ServerRequestFactory::class)->createFromGlobals();

        if (version_compare(phpversion(), '7.0') < 0) {
            set_exception_handler([$this, 'handleException']);
            set_error_handler([$this, 'handleError']);
            register_shutdown_function([$this, 'handleShutdown']);
//            ini_set('error_reporting', 'OFF');
        }
        
        try {
            $response = $this->dispatch($app->get(Dispatcher::class), $request);
        } catch (Throwable $exception) {
            return $this->handleException($exception);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->sendToGlobal($response);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return int
     */
    protected function sendToGlobal(ResponseInterface $response)
    {
        /* @var \Wandu\Http\Psr\Sender\ResponseSender $sender */
        $sender = $this->app->get(ResponseSender::class);
        $sender->sendToGlobal($response);
        return 0;
    }

    /**
     * @param \Wandu\Router\Dispatcher $dispatcher
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Wandu\Http\Exception\MethodNotAllowedException
     * @throws \Wandu\Http\Exception\NotFoundException
     */
    protected function dispatch(Dispatcher $dispatcher, ServerRequestInterface $request)
    {
        $dispatcher = $dispatcher->withRoutes(function (Router $router) {
            $this->definition->routes($router);
        });
        try {
            return $dispatcher->dispatch($request);
        } catch (RouteNotFoundException $exception) {
            throw new NotFoundException();
        } catch (RouteMethodException $exception) {
            throw new MethodNotAllowedException();
        }
    }

    /**
     * @param \Exception|\Throwable $exception
     * @return int
     */
    public function handleException($exception)
    {
        /* @var \Wandu\Foundation\Contracts\HttpErrorHandlerInterface $handler */
        $handler = $this->app->get(HttpErrorHandlerInterface::class);
        $this->sendToGlobal($handler->handle($this->request, $exception));
        return -1;
    }
    
    /**
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = null, $line = null)
    {
        if ($level & error_reporting()) {
            throw new ErrorException($message, $level, $level, $file, $line);
        }
    }
    
    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error && self::isLevelFatal($error['type'])) {
            $this->handleException(
                new ErrorException(
                    $error['message'],
                    $error['type'],
                    $error['type'],
                    $error['file'],
                    $error['line']
                )
            );
        }
    }
    
    protected static function isLevelFatal($level)
    {
        $errors = E_ERROR;
        $errors |= E_PARSE;
        $errors |= E_CORE_ERROR;
        $errors |= E_CORE_WARNING;
        $errors |= E_COMPILE_ERROR;
        $errors |= E_COMPILE_WARNING;
        return ($level & $errors) > 0;
    }
}
