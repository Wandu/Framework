<?php
namespace Wandu\Foundation\Kernels;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\HttpErrorHandlerInterface;
use Wandu\Foundation\Error\DefaultHttpErrorHandler;
use Wandu\Http\Factory\ServerRequestFactory;
use Wandu\Http\Sender\ResponseSender;
use Wandu\Router\Dispatcher;

class HttpRouterKernel extends KernelAbstract
{
    /** @var \Psr\Http\Message\ServerRequestInterface */
    protected $request;
    
    /**
     * {@inheritdoc}
     */
    public function execute(ContainerInterface $app)
    {
        $this->useErrorHandling();
        if (!$app->has(HttpErrorHandlerInterface::class)) {
            $app->bind(HttpErrorHandlerInterface::class, DefaultHttpErrorHandler::class);
        }

        /* @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $this->createRequest($app);

        try {
            $response = $this->dispatch($app->get(Dispatcher::class), $request);
        } catch (Throwable $exception) {
            return $this->handleException($exception);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->sendResponse($response);
    }

    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createRequest(ContainerInterface $app)
    {
        return $this->request = $app->get(ServerRequestFactory::class)->createFromGlobals();
    }
    
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return int
     */
    protected function sendResponse(ResponseInterface $response)
    {
        /* @var \Wandu\Http\Sender\ResponseSender $sender */
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
        $routes = isset($this->attributes['routes']) ? $this->attributes['routes'] : null;
        if ($routes) {
            $dispatcher->setRoutes($routes);
        }
        return $dispatcher->dispatch($request);
    }

    /**
     * @param \Exception|\Throwable $exception
     * @return int
     */
    public function handleException($exception)
    {
        // output buffer clean
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        /* @var \Wandu\Foundation\Contracts\HttpErrorHandlerInterface $handler */
        $handler = $this->app->get(HttpErrorHandlerInterface::class);
        $this->sendResponse($handler->handle($this->request, $exception));
        return -1;
    }
}
