<?php
namespace Wandu\Router\Stubs;

use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function login(ServerRequestInterface $request)
    {
        return "login@Auth, cookie=" . json_encode($request->getAttribute('cookie', []));
    }
}
