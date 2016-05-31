<?php
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Application;
use Wandu\Foundation\Contracts\DefinitionInterface;
use Wandu\Foundation\Kernels\HttpRouterKernel;
use Wandu\Router\Router;

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (is_file(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
} elseif (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}

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

exit(
    (new Application(
        new HttpRouterKernel($definition)
    ))->execute()
);
