<?php return array (
  'dispatch_data' => 
  array (
    0 => 
    array (
      'GET' => 
      array (
        '/admin' => 'GET,HEAD/admin',
      ),
      'HEAD' => 
      array (
        '/admin' => 'GET,HEAD/admin',
      ),
    ),
    1 => 
    array (
    ),
  ),
  'routes' => 
  array (
    'GET,HEAD/admin' => 
    Wandu\Router\Route::__set_state(array(
       'className' => 'Wandu\\Router\\stubs\\AdminController',
       'methodName' => 'index',
       'middlewares' => 
      array (
      ),
    )),
  ),
);