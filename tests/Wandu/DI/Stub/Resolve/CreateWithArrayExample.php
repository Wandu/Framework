<?php
namespace Wandu\DI\Stub\Resolve;

use Wandu\DI\Stub\Resolve\DependInterface;

class CreateWithArrayExample
{
    /** @var \Wandu\DI\Stub\Resolve\DependInterface */
    protected $depend;

    /** @var array */
    protected $configs;

    /**
     * @param \Wandu\DI\Stub\Resolve\DependInterface $depend
     * @param array $configs
     */
    public function __construct(DependInterface $depend, array $configs)
    {
        $this->depend = $depend;
        $this->configs = $configs;
    }

    /**
     * @return \Wandu\DI\Stub\Resolve\DependInterface
     */
    public function getDepend()
    {
        return $this->depend;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }
}
