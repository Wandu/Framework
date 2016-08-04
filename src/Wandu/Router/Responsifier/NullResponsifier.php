<?php
namespace Wandu\Router\Responsifier;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Wandu\Router\Contracts\ResponsifierInterface;

class NullResponsifier implements ResponsifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function responsify($response)
    {
        if ($response instanceof ResponseInterface) {
            return $response;
        }
        throw new RuntimeException('Unsupported Type of Response.');
    }
}
