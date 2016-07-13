<?php
use Wandu\Foundation\Application;
use Wandu\Foundation\Definitions\StandardDefinition;
use Wandu\Foundation\Kernels\HttpRouterKernel;

$searchDir = realpath(__DIR__ . '/..');
while (!file_exists($searchDir . '/vendor/autoload.php')) {
    if ($searchDir == '/' || !$searchDir) {
        header('HTTP/1.1 500 Internal Server Error');
        echo "cannot find autoload.php. you may run composer install.";
        exit(-1);
    }
    $searchDir = dirname($searchDir);
}
define('WANDU_PATH', $searchDir);

require_once $searchDir . '/vendor/autoload.php';

$defininitionPath = $searchDir . '/.wandu.php';

if (file_exists($defininitionPath)) {
    $definition = require $defininitionPath;
} else {
    $definition = new StandardDefinition();
}

$app = new Application(new HttpRouterKernel($definition));
$app->instance('base_path', $searchDir);
exit($app->execute());
