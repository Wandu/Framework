<?php
namespace Wandu\Router;

class Configuration
{
    /** @var array */
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config + [
                'virtual_method_enabled' => false,
                'cache_enabled' => false,
                'cache_file' => null,
            ];
    }

    /**
     * @return boolean
     */
    public function isVirtualMethodEnabled()
    {
        return $this->config['virtual_method_enabled'];
    }

    /**
     * @return boolean
     */
    public function isCacheEnabled()
    {
        return $this->config['cache_enabled'];
    }

    /**
     * @return string
     */
    public function getCacheFile()
    {
        return $this->config['cache_file'];
    }
}
