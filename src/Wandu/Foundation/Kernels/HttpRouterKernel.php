<?php
namespace Wandu\Foundation\Kernels;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\ConfigInterface;
use Wandu\Foundation\KernelInterface;
use Wandu\Http\Exception\HttpException;
use Wandu\Http\Exception\MethodNotAllowedException;
use Wandu\Http\Exception\NotFoundException;
use Wandu\Http\Psr\Factory\ServerRequestFactory;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\Sender\ResponseSender;
use Wandu\Http\Psr\Stream\StringStream;
use Wandu\Router\Dispatcher;
use Wandu\Router\Exception\MethodNotAllowedException as RouteMethodException;
use Wandu\Router\Exception\RouteNotFoundException;

class HttpRouterKernel implements KernelInterface
{
    /** @var \Wandu\Foundation\ConfigInterface */
    private $config;

    /**
     * @param \Wandu\Foundation\ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
        $this->config->providers($app);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContainerInterface $app)
    {
        // @todo remove ErrorHandlerProvider
        // @todo add Error handler, and Whoops pretty when debug === true in Error Handler.
        $request = $this->createRequest($app);
        try {
            $response = $this->dispatch($app->get(Dispatcher::class), $request);
            $app->get(ResponseSender::class)->sendToGlobal($response);
        } catch (Exception $exception) {
            if ($exception instanceof HttpException) {
                $httpException = $exception;
            } else {
                // if not HttpException and debug mode, exception will be prettify(by Whoops).
                if ($app['config']->get('debug', true)) {
                    throw $exception;
                }
                $httpException = new HttpException();
            }
            $handler = $app['config']->get('error.handler');
            if ($handler) {
                $response = $this->responsify(
                    $app->create($handler)->handle($request, $exception),
                    $httpException
                );
            } else {
                $response = $httpException->toResponse();
            }
            // body is ''
            if (!$response->getBody()) {
                $body = $response->getReasonPhrase();
                $response = $response->withBody(new Stringstream($body));
            }
            $app->get(ResponseSender::class)->sendToGlobal($response);
        }
    }

    /**
     * @param \Wandu\DI\ContainerInterface $app
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createRequest(ContainerInterface $app)
    {
        return $app->get(ServerRequestFactory::class)->fromGlobals();
    }

    /**
     * @param \Wandu\Router\Dispatcher $dispatcher
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     * @throws \Wandu\Http\Exception\MethodNotAllowedException
     * @throws \Wandu\Http\Exception\NotFoundException
     */
    protected function dispatch(Dispatcher $dispatcher, ServerRequestInterface $request)
    {
        $dispatcher = $dispatcher->withRoutes($this->config);
        try {
            return $dispatcher->dispatch($request);
        } catch (RouteNotFoundException $exception) {
            throw new NotFoundException();
        } catch (RouteMethodException $exception) {
            throw new MethodNotAllowedException();
        }
    }

    /**
     * @param mixed $response
     * @param \Wandu\Http\Exception\HttpException $exception
     * @return \Wandu\Http\Psr\Response
     */
    protected function responsify($response, HttpException $exception)
    {
        if ($response instanceof ResponseInterface) {
            return $response;
        }
        $isJson = false;
        if (!isset($response)) {
            $body = $exception->getReasonPhrase();
        } elseif (!is_string($response) && !is_numeric($response)) {
            $body = json_encode($response);
            $isJson = true;
        } else {
            $body = $response;
        }
        $responseToReturn = new Response(
            $exception->getStatusCode(),
            $exception->getReasonPhrase(),
            '1.1',
            [],
            new StringStream($body)
        );
        if ($isJson) {
            $responseToReturn = $responseToReturn->withHeader('Content-Type', 'application/json');
        }
        return $responseToReturn;
    }
}
