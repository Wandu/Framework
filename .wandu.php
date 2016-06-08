<?php
use Wandu\Foundation\Definitions\FullDefinition;
use Wandu\Router\Controllers\HelloWorldController;
use Wandu\Router\Router;

return new class extends FullDefinition
{
    /**
     * {@inheritdoc}
     */
    public function routes(Router $router)
    {
        $router->get('/', HelloWorldController::class);
    }
};
