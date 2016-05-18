<?php
namespace Wandu\Config\Contracts;

use ArrayAccess;

interface ConfigInterface extends ArrayAccess
{
    /**
     * @return array
     */
    public function getRawData();

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);

    /**
     * @param string $name
     */
    public function remove($name);

    /**
     * @param string $key
     * @return bool
     */
    public function has($key);
}
