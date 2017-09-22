<?php
namespace Wandu\Foundation\WebApp;

use Throwable;
use Wandu\Config\ConfigServiceProvider;
use Wandu\Config\Contracts\Config;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Contracts\Bootstrap as BootstrapContract;
use Wandu\Foundation\WebApp\Contracts\HttpErrorHandler;
use Wandu\Http\Factory\ServerRequestFactory;
use Wandu\Http\HttpServiceProvider;
use Wandu\Http\Sender\ResponseSender;
use Wandu\Router\Contracts\Routable;
use Wandu\Router\Dispatcher;
use Wandu\Router\RouterServiceProvider;

abstract class Bootstrap implements BootstrapContract
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
        $this->registerConfiguration($app->get(Config::class));
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
            $dispatcher = $app->get(Dispatcher::class);
            $this->setRoutes($routeCollection = $dispatcher->createRouteCollection());
            $response = $dispatcher->dispatch($routeCollection->compile(), $request);
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

    /**
     * @param \Wandu\Config\Contracts\Config $config
     */
    abstract public function registerConfiguration(Config $config);

    /**
     * @param \Wandu\Router\Contracts\Routable $router
     */
    abstract public function setRoutes(Routable $router);
}
