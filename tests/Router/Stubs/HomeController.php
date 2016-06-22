<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    public function index(ServerRequestInterface $request)
    {
        return "[{$request->getMethod()}] index@Home";
    }
}
