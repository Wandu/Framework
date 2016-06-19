<?php
use Wandu\Foundation\Application;
use Wandu\Foundation\Definitions\StandardDefinition;
use Wandu\Foundation\Kernels\HttpRouterKernel;

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
    $definition = new StandardDefinition();
}

$app = new Application(new HttpRouterKernel($definition));
$app->instance('base_path', WANDU_PATH);
exit($app->execute());
