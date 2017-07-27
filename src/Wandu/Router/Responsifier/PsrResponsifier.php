<?php
namespace Wandu\Router\Responsifier;

use Psr\Http\Message\ResponseInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use function Wandu\Http\response;

class PsrResponsifier implements ResponsifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function responsify($response): ResponseInterface
    {
        return response()->auto($response);
    }
}
