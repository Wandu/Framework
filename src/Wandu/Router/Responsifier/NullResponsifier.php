<?php
namespace Wandu\Router\Responsifier;

use Psr\Http\Message\ResponseInterface;
use Wandu\Router\Contracts\ResponsifierInterface;

class NullResponsifier implements ResponsifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function responsify($response): ResponseInterface
    {
        return $response;
    }
}
