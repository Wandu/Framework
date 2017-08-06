<?php
namespace Wandu\Config\Contracts;

use ArrayAccess;

interface Config extends ArrayAccess
{
    /**
     * @param string $path
     */
    public function load(string $path);

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param string $name
     * @return \Wandu\Config\Contracts\Config
     */
    public function subset($name): Config;

    /**
     * @param string $key
     * @return bool
     */
    public function has($key): bool;

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null);
}
