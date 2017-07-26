<?php
namespace Wandu\Router\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ResponsifierInterface
{
    /**
     * @param mixed $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function responsify($response): ResponseInterface;
}
