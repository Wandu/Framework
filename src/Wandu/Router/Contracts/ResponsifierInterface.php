<?php
namespace Wandu\Router\Contracts;

interface ResponsifierInterface
{
    /**
     * @param mixed $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function responsify($response);
}
