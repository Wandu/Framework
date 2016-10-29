<?php
namespace Wandu\Router;

class Configuration
{
    /** @var array */
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config + [
                'middleware' => [],
                'virtual_method_enabled' => false,
                'cache_enabled' => false,
                'cache_file' => null,
            ];
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        return $this->config['middleware'];
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
