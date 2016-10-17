<?php
namespace Wandu\App\Http\Controllers;

use Wandu\View;

class HelloWorldController
{
    public function index()
    {
        return View\render('welcome.php', [
            'message' => 'Hello Wandu'
        ]);
    }
}
