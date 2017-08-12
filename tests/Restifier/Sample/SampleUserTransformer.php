<?php
namespace Wandu\Restifier\Sample;

use Wandu\Restifier\Contracts\Restifiable;

class SampleUserTransformer
{
    public function __invoke(SampleUser $user, Restifiable $restifier, array $includes = [])
    {
        return [
            'username' => $user->username,
            'customer' => $restifier->restify($user->customer),
        ];
    }

    public function customer(SampleUser $user, Restifiable $restifier, array $includes = [])
    {
        return [
            'customer' => $restifier->restify($user->customer, $includes),
        ];
    }

    public function profile(SampleUser $user, Restifiable $restifier, array $includes = [])
    {
        return [
            'profile' => $user->profile,
        ];
    }
}
