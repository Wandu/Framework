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

require_once $autoloadPath . '/vendor/autoload.php';
chdir($autoloadPath);
unset($autoloadPath);

if (!file_exists('.wandu.php')) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "cannot find .wandu.php. you may re-install wandu.";
    exit(-1);
}

$definition = require '.wandu.php';

$app = new Application(new HttpRouterKernel($definition));
exit($app->execute());
