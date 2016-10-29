<?php
namespace Wandu\Foundation\Contracts;

interface DefinitionInterface
{
    /**
     * @return array
     */
    public function configs();

    /**
     * @return array
     */
    public function providers();
}
