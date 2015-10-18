<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\ControllerInterface;

class AdminController implements ControllerInterface
{
    /**
     * @return string
     */
    public function index()
    {
        return "index@Admin";
    }

    /**
     * @return string
     */
    public function action()
    {
        return "action@Admin";
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string
     */
    public function doit(ServerRequestInterface $request)
    {
        return "doit@Admin, " . $request->getAttribute('action');
    }
}
