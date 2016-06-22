<?php
use Wandu\Foundation\Definitions\StandardDefinition;
use Wandu\Router\Controllers\HelloWorldController;
use Wandu\Router\Router;

return new class extends StandardDefinition
{
    /**
     * {@inheritdoc}
     */
    public function configs()
    {
        return require __DIR__ . '/.wandu.config.php';
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router)
    {
        $router->get('/', HelloWorldController::class);
    }
};
