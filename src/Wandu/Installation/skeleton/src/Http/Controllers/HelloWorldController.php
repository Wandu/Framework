<?php
namespace YourOwnApp\Http\Controllers;

use Wandu\View;

class HelloWorldController
{
    public function index()
    {
        return View\render('welcome.latte', [
            'message' => 'Hello Wandu'
        ]);
    }
}
