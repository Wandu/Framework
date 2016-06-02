<?php
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Application;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\Kernels\HttpRouterKernel;
use Wandu\Router\Router;

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    define('WANDU_PATH', realpath(__DIR__ . '/..'));
} elseif (is_file(__DIR__ . '/../../../../vendor/autoload.php')) {
    define('WANDU_PATH', realpath(__DIR__ . '/../../../..'));
} elseif (is_file(__DIR__ . '/../../vendor/autoload.php')) {
    define('WANDU_PATH', realpath(__DIR__ . '/../../../..'));
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo "need to run composer install.";
    exit;
}
require_once WANDU_PATH . '/vendor/autoload.php';

$defininitionPath = dirname(__DIR__) . '/.wandu.php';
if (file_exists($defininitionPath)) {
    $definition = require $defininitionPath;
} else {
    $definition = new class implements DefinitionInterface {
        public function providers(ContainerInterface $app) {}
        public function commands(Dispatcher $dispatcher) {}
        public function routes(Router $router) {}
    };
}

$app = new Application(new HttpRouterKernel($definition));
$app->instance('base_path', WANDU_PATH);
exit($app->execute());
