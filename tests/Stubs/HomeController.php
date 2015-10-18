<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\ControllerInterface;

class HomeController implements ControllerInterface
{
    /**
     * @return string
     */
    public function index()
    {
        return "index@Home";
    }

    public function login(ServerRequestInterface $request)
    {
        return $request->getAttribute('cookie', 'null');
    }
}
