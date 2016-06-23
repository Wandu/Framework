<?php
use Wandu\Foundation\Definitions\StandardDefinition;
use Wandu\Router\Router;

use %%namespace%%\Controllers\HelloWorldController;

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
