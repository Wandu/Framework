<?php
namespace Wandu\Migrator;

class Configuration
{
    /** @var array */
    protected $config;
    
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return isset($this->config['path']) ? $this->config['path'] : null;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return isset($this->config['table']) ? $this->config['table'] : null;
    }

    /**
     * @return string
     */
    public function getConnection()
    {
        return isset($this->config['connection']) ? $this->config['connection'] : null;
    }
}
