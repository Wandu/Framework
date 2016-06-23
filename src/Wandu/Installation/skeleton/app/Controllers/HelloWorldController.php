<?php
namespace %%namespace%%\Controllers;

use Wandu\View;

class HelloWorldController
{
    public function index()
    {
        return View\render('welcome', [
            'message' => 'Hello Wandu'
        ]);
    }
}
