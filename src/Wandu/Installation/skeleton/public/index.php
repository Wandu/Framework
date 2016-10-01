<?php
use Wandu\Foundation\Application;
use Wandu\Foundation\Kernels\HttpRouterKernel;

$autoloadPath = realpath(__DIR__);
while (!file_exists($autoloadPath . '/vendor/autoload.php')) {
    if ($autoloadPath == '/' || !$autoloadPath) {
        header('HTTP/1.1 500 Internal Server Error');
        echo "cannot find autoload.php. you may run composer install.";
        exit(-1);
    }
    $autoloadPath = dirname($autoloadPath);
}

define('WANDU_BASE_PATH', $autoloadPath);
require_once WANDU_BASE_PATH . '/vendor/autoload.php';
unset($autoloadPath);

$appPath = realpath(getcwd());
while (!file_exists($appPath . '/.wandu.php')) {
    if ($appPath == '/' || !$appPath) {
        header('HTTP/1.1 500 Internal Server Error');
        echo "cannot find .wandu.php. you may re-install wandu.";
        exit(-1);
    }
    $appPath = dirname($appPath);
}

if (isset($appPath)) {
    $definition = require $appPath . '/.wandu.php';
} else {
    $definition = new NeedInstallDefinition();
}
define('WANDU_APP_PATH', $appPath);
unset($appPath);

$app = new Application(new HttpRouterKernel($definition));
$app->instance('base_path', WANDU_BASE_PATH);
$app->instance('app_path', WANDU_APP_PATH);
exit($app->execute());
