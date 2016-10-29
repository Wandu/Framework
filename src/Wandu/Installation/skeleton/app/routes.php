<?php
use Wandu\Router\Router;
use YourOwnApp\Http\Controllers\HelloWorldController;

return function (Router $router) {
    $router->get('/', HelloWorldController::class);
};
