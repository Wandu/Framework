<?php
namespace Wandu\Router\Controllers;

class HelloWorldController
{
    public function index()
    {
        return \Wandu\View\render('welcome', [
            'message' => 'Hello Wandu'
        ]);
    }
}
