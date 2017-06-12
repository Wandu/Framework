<?php
namespace Wandu\Foundation\Bootstrap;

use Throwable;
use Wandu\Config\ConfigServiceProvider;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\Bootstrap;
use Wandu\Foundation\Contracts\HttpErrorHandler;
use Wandu\Foundation\Error\DefaultHttpErrorHandler;
use Wandu\Http\Factory\ServerRequestFactory;
use Wandu\Http\HttpServiceProvider;
use Wandu\Http\Sender\ResponseSender;
use Wandu\Router\Dispatcher;
use Wandu\Router\RouterServiceProvider;

class HttpRouterBootstrap implements Bootstrap
{
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

        $request = $app->get(ServerRequestFactory::class)->createFromGlobals();
        
        try {
            $response = $app->get(Dispatcher::class)->dispatch($request);
        } catch (Throwable $exception) {
            // output buffer clean
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $errorHandler = $app->get(HttpErrorHandler::class);
            $app->get(ResponseSender::class)->sendToGlobal($errorHandler->handle($request, $exception));
            return -1;
        }

        $app->get(ResponseSender::class)->sendToGlobal($response);
        return 0;
    }
}
