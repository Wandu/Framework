<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;

class AdminController
{
    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function index(ServerRequestInterface $request)
    {
        return "index@AdminController string";
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function action(ServerRequestInterface $request)
    {
        return "action@AdminController string";
    }
}
