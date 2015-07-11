<?php
namespace Wandu\DI\Stub;

class StubClientWithConfig
{
    /** @var DepInterface */
    protected $dependency;

    /** @var array */
    protected $config;

    /**
     * @param DepInterface $dependency
     * @param array $config
     */
    public function __construct(DepInterface $dependency, array $config)
    {
        $this->dependency = $dependency;
        $this->config = $config;
    }

    /**
     * @return DepInterface
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
