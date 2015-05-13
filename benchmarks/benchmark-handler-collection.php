<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Jicjjang\June\Router;
use Jicjjang\June\Stubs\AdminController;
use Psr\Http\Message\RequestInterface;

$bench = new Ubench();

$bench->start();
/****************************************************************/

$getMock = Mockery::mock(RequestInterface::class);
$getMock->shouldReceive('getMethod')->andReturn('GET');
$getMock->shouldReceive('getUri->getPath')->andReturn('/abc/ded');

for ($i = 0; $i < 1000; $i++) {
    $app = new Router();
    $app->setController('admin', new AdminController());

    $app->get('/abc', function () {}, function () {});
    $app->get('/abc/{id}/{name}', ["admin", "middleware"], function () {});
    $app->get('/abd/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->get('/abc/def', ["admin", "middleware"], ['admin', 'action']);
    $app->get('/abc/ghi', ["admin", "middleware"], ['admin', 'action'], function () {});
    $app->get('/def/{id}/2', function () {}, function () {});
    $app->get('/def/{id}/{name}', ["admin", "middleware"], function () {});
    $app->get('/defg/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->get('/def/def/{shit}', ["admin", "action"], ['admin', 'middleware']);
    $app->get('/def/3/{zzz}', ["admin", "middleware"], ['admin', 'action'], function () {});

    $app->post('/abc', function () {}, function () {});
    $app->post('/abc/{id}/{name}', ["admin", "middleware"], function () {});
    $app->post('/abd/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->post('/abc/def', ["admin", "middleware"], ['admin', 'action']);
    $app->post('/abc/ghi', ["admin", "middleware"], ['admin', 'action'], function () {});
    $app->post('/def/{id}/2', function () {}, function () {});
    $app->post('/def/{id}/{name}', ["admin", "middleware"], function () {});
    $app->post('/defg/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->post('/def/def/{shit}', ["admin", "action"], ['admin', 'middleware']);
    $app->post('/def/3/{zzz}', ["admin", "middleware"], ['admin', 'action'], function () {});

    $app->put('/abc', function () {}, function () {});
    $app->put('/abc/{id}/{name}', ["admin", "middleware"], function () {});
    $app->put('/abd/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->put('/abc/def', ["admin", "middleware"], ['admin', 'action']);
    $app->put('/abc/ghi', ["admin", "middleware"], ['admin', 'action'], function () {});
    $app->put('/def/{id}/2', function () {}, function () {});
    $app->put('/def/{id}/{name}', ["admin", "middleware"], function () {});
    $app->put('/defg/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->put('/def/def/{shit}', ["admin", "action"], ['admin', 'middleware']);
    $app->put('/def/3/{zzz}', ["admin", "middleware"], ['admin', 'action'], function () {});

    $app->options('/abc', function () {}, function () {});
    $app->options('/abc/{id}/{name}', ["admin", "middleware"], function () {});
    $app->options('/abd/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->options('/abc/def', ["admin", "middleware"], ['admin', 'action']);
    $app->options('/abc/ghi', ["admin", "middleware"], ['admin', 'action'], function () {});
    $app->options('/def/{id}/2', function () {}, function () {});
    $app->options('/def/{id}/{name}', ["admin", "middleware"], function () {});
    $app->options('/defg/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->options('/def/def/{shit}', ["admin", "action"], ['admin', 'middleware']);
    $app->options('/def/3/{zzz}', ["admin", "middleware"], ['admin', 'action'], function () {});

    $app->any('/abc', function () {}, function () {});
    $app->any('/abc/{id}/{name}', ["admin", "middleware"], function () {});
    $app->any('/abd/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->any('/abc/def', ["admin", "middleware"], ['admin', 'action']);
    $app->any('/abc/ghi', ["admin", "middleware"], ['admin', 'action'], function () {});
    $app->any('/def/{id}/2', function () {}, function () {});
    $app->any('/def/{id}/{name}', ["admin", "middleware"], function () {});
    $app->any('/defg/{id}/{what}', function () {}, ["admin", "middleware"]);
    $app->any('/def/def/{shit}', ["admin", "action"], ['admin', 'middleware']);
    $app->any('/def/3/{zzz}', ["admin", "middleware"], ['admin', 'action'], function () {});
    try {
        $app->dispatch($getMock);
    } catch (RuntimeException $e) {

    }
}
/****************************************************************/
$bench->end();

// Get elapsed time and memory
echo $bench->getTime()."\n"; // 156ms or 1.123s
echo $bench->getTime(true)."\n"; // elapsed microtime in float
echo $bench->getTime(false, '%d%s')."\n"; // 156ms or 1s

echo $bench->getMemoryPeak()."\n"; // 152B or 90.00Kb or 15.23Mb
echo $bench->getMemoryPeak(true)."\n"; // memory peak in bytes
echo $bench->getMemoryPeak(false, '%.3f%s')."\n"; // 152B or 90.152Kb or 15.234Mb

// Returns the memory usage at the end mark
echo $bench->getMemoryUsage()."\n"; // 152B or 90.00Kb or 15.23Mb