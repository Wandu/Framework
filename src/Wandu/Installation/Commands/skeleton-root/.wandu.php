<?php
use Wandu\Foundation\Definitions\StandardDefinition;
use Wandu\Router\Router;
use Wandu\DI\ContainerInterface;

use ___NAMESPACE___\ApplicationServiceProvider;
use ___NAMESPACE___\Controllers\HelloWorldController;

return new class extends StandardDefinition
{
    public function configs()
    {
        return require __DIR__ . '/.wandu.config.php';
    }

    /**
     * {@inheritdoc}
     */
    public function providers(ContainerInterface $app)
    {
        parent::providers($app);
        $app->register(new ApplicationServiceProvider());
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router)
    {
        $router->get('/', HelloWorldController::class);
    }
};
