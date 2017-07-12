<?php
namespace Wandu\Router\Contracts;

interface RouteInformation
{
    /**
     * @return array
     */
    public function getDomains(): array;

    /**
     * @return string
     */
    public function getClassName(): string;

    /**
     * @return string
     */
    public function getMethodName(): string;

    /**
     * @return array
     */
    public function getMiddlewares(): array;
}
