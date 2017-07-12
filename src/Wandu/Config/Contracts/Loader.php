<?php
namespace Wandu\Config\Contracts;

interface Loader
{
    /**
     * @param string $path
     * @return bool
     */
    public function test(string $path): bool;

    /**
     * @param string $path
     * @return array|null
     */
    public function load(string $path);
}
