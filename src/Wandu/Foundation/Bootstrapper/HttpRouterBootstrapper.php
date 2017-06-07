<?php
namespace Wandu\Foundation\Bootstrapper;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Wandu\Config\ConfigServiceProvider;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\Bootstrapper;
use Wandu\Foundation\Contracts\HttpErrorHandler;
use Wandu\Foundation\Error\DefaultHttpErrorHandler;
use Wandu\Http\Factory\ServerRequestFactory;
use Wandu\Http\HttpServiceProvider;
use Wandu\Http\Sender\ResponseSender;
use Wandu\Router\Dispatcher;
use Wandu\Router\RouterServiceProvider;

class HttpRouterBootstrapper implements Bootstrapper
{
    /** @var \Closure */
    protected $routes;

    public function __construct(Closure $routes = null)
    {
        $this->routes = $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function providers(): array
    {
        return [
            new ConfigServiceProvider(),
            new HttpServiceProvider(),
            new RouterServiceProvider(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function boot(ContainerInterface $app)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContainerInterface $app): int
    {
        if (!$app->has(HttpErrorHandler::class)) {
            $app->bind(HttpErrorHandler::class, DefaultHttpErrorHandler::class);
        }

        $requestFactory = $app->get(ServerRequestFactory::class);
        $responseSender = $app->get(ResponseSender::class);

        $request = $requestFactory->createFromGlobals();
        
        try {
            $response = $this->dispatch($app->get(Dispatcher::class), $request);
        } catch (Throwable $exception) {

            // output buffer clean
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $errorHandler = $app->get(HttpErrorHandler::class);
            $responseSender->sendToGlobal($errorHandler->handle($request, $exception));
            return -1;
        }

        $responseSender->sendToGlobal($response);
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
        if ($this->routes) {
            $dispatcher->setRoutes($this->routes);
        }
        return $dispatcher->dispatch($request);
    }
}
