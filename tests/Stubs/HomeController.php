<?php
namespace Wandu\Router\Stubs;

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
}
