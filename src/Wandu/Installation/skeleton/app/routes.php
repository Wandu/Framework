<?php
use Wandu\Router\Router;
use WanduSkeleton\Http\Controllers\HelloWorldController;

return function (Router $router) {
    // your routes
    $router->get('/', HelloWorldController::class);
};
