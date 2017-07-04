<?php
namespace Wandu\Router\Responsifier;

use Wandu\Router\Contracts\ResponsifierInterface;
use function Wandu\Http\response;

class PsrResponsifier implements ResponsifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function responsify($response)
    {
        return response()->auto($response);
    }
}
