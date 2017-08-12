<?php
namespace Wandu\Restifier\Sample;

use Wandu\Restifier\Contracts\Restifiable;

class SampleUserTransformer
{
    public function __invoke(SampleUser $user, Restifiable $restifier, array $includes = [])
    {
        return [
            'username' => $user->username,
            'customer' => $restifier->restify($user->customer, $includes),
        ];
    }
    
    public function profile(SampleUser $user)
    {
        return [
            'profile' => $user->profile,
        ];
    }
}
