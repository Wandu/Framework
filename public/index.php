<?php
use Wandu\Console\Dispatcher;
use Wandu\DI\ContainerInterface;
use Wandu\Foundation\Application;
use Wandu\Foundation\ConfigInterface;
use Wandu\Foundation\Kernels\HttpRouterKernel;
use Wandu\Router\Router;

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (is_file(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
} elseif (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}

$configFile = dirname(__DIR__) . '/.wandu.php';
if (file_exists($configFile)) {
    $config = require $configFile;
} else {
    $config = new class implements ConfigInterface {
        public function providers(ContainerInterface $app) {}
        public function commands(Dispatcher $dispatcher) {}
        public function routes(Router $router) {}
    };
}

$app = new Application(new HttpRouterKernel($config));
$app->boot();
exit($app->execute());
