<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Controller\ControllerInterface;

class AdminController implements ControllerInterface
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

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public function hello(ServerRequestInterface $request)
    {
        return "hello@AdminController string";
    }
}
