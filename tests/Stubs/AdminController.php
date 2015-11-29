<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;

class AdminController
{
    public function index(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] index@Admin";
    }

    public function action(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] action@Admin";
    }
}
